<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services\Import;

use Doctrine\ORM\EntityManagerInterface;
use FantasyAcademy\API\Entity\Challenge;
use FantasyAcademy\API\Exceptions\ImportFailed;
use FantasyAcademy\API\Exceptions\ImportResultsWarning;
use FantasyAcademy\API\Exceptions\PlayerChallengeAnswerNotFound;
use FantasyAcademy\API\Repository\PlayerChallengeAnswerRepository;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Psr\Clock\ClockInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

readonly final class ChallengesResultsImport
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PlayerChallengeAnswerRepository $playerChallengeAnswerRepository,
        private ClockInterface $clock,
    ) {
    }

    /**
     * @throws ImportResultsWarning
     * @throws ImportFailed
     */
    public function importFile(UploadedFile $file): void
    {
        $path = $file->getPathname();
        
        $spreadsheet = IOFactory::load($path);
        
        if (!$spreadsheet->sheetNameExists('Points')) {
            throw new ImportFailed('Sheet "Points" not found in the uploaded file.');
        }
        
        $worksheet = $spreadsheet->getSheetByName('Points');
        
        if ($worksheet === null) {
            throw new ImportFailed('Could not access the "Points" sheet.');
        }
        
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        
        $headerRowData = $worksheet->rangeToArray('A1:' . $highestColumn . '1', null, true, false);
        if (empty($headerRowData)) {
            throw new ImportFailed('Could not read header row from the Points sheet.');
        }
        $headerRow = $headerRowData[0];
        
        $playerChallengeAnswerIdColumnIndex = array_search('id', $headerRow);
        $pointsColumnIndex = array_search('points', $headerRow);

        if ($playerChallengeAnswerIdColumnIndex === false) {
            throw new ImportFailed('Column "id" not found in the Points sheet.');
        }
        
        if ($pointsColumnIndex === false) {
            throw new ImportFailed('Column "points" not found in the Points sheet.');
        }

        /** @var array<string, Challenge> $challenges */
        $challenges = [];
        $missingIds = [];
        $importedCount = 0;
        
        for ($row = 2; $row <= $highestRow; $row++) {
            if (!is_int($playerChallengeAnswerIdColumnIndex) || !is_int($pointsColumnIndex)) {
                continue;
            }
            
            $playerChallengeAnswerIdColumnLetter = $this->convertColumnIndexToLetter($playerChallengeAnswerIdColumnIndex);
            $pointsColumnLetter = $this->convertColumnIndexToLetter($pointsColumnIndex);

            $playerChallengeAnswerIdValue = $worksheet->getCell($playerChallengeAnswerIdColumnLetter . $row)->getValue();
            $pointsValue = $worksheet->getCell($pointsColumnLetter . $row)->getCalculatedValue();

            if ($playerChallengeAnswerIdValue === null || $playerChallengeAnswerIdValue === '') {
                continue;
            }

            $playerChallengeAnswerIdString = is_scalar($playerChallengeAnswerIdValue) ? trim((string) $playerChallengeAnswerIdValue) : '';

            if ($playerChallengeAnswerIdString === '') {
                continue;
            }

            // Validate UUID format (lenient - accepts any UUID-like string, not just RFC 4122)
            if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $playerChallengeAnswerIdString)) {
                $missingIds[] = $playerChallengeAnswerIdString;
                continue;
            }

            $playerChallengeAnswerId = Uuid::fromString($playerChallengeAnswerIdString);

            try {
                $playerChallengeAnswer = $this->playerChallengeAnswerRepository->get($playerChallengeAnswerId);
            } catch (PlayerChallengeAnswerNotFound) {
                $missingIds[] = $playerChallengeAnswerIdString;
                continue;
            }

            $challenge = $playerChallengeAnswer->challenge;
            $challenges[$challenge->id->toString()] = $challenge;

            $points = is_scalar($pointsValue) ? (int) $pointsValue : 0;
            $playerChallengeAnswer->evaluate($points);
            $importedCount++;
        }

        foreach ($challenges as $challenge) {
            $challenge->evaluate($this->clock->now());
        }
        
        $this->entityManager->flush();

        if (!empty($missingIds)) {
            throw new ImportResultsWarning($missingIds, $importedCount);
        }
    }

    private function convertColumnIndexToLetter(int $columnIndex): string
    {
        // Converting `A` to 65 + column index to translate A + 1 to B for example (column name)
        return chr(ord('A') + $columnIndex);
    }
}
