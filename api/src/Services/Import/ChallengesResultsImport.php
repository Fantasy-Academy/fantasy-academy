<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services\Import;

use Doctrine\ORM\EntityManagerInterface;
use FantasyAcademy\API\Exceptions\ImportFailed;
use FantasyAcademy\API\Exceptions\ImportResultsWarning;
use FantasyAcademy\API\Exceptions\PlayerChallengeAnswerNotFound;
use FantasyAcademy\API\Repository\ChallengeRepository;
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
        private ChallengeRepository $challengeRepository,
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
        
        $idColumnIndex = array_search('id', $headerRow);
        $pointsColumnIndex = array_search('points', $headerRow);
        $challengeIdColumnIndex = array_search('challenge_id', $headerRow);

        if ($idColumnIndex === false) {
            throw new ImportFailed('Column "id" not found in the Points sheet.');
        }
        
        if ($pointsColumnIndex === false) {
            throw new ImportFailed('Column "points" not found in the Points sheet.');
        }

        if ($challengeIdColumnIndex === false) {
            throw new ImportFailed('Column "challenge_id" not found in the Points sheet.');
        }

        /** @var array<string, string> $challengeIds */
        $challengeIds = [];
        $missingIds = [];
        $importedCount = 0;
        
        for ($row = 2; $row <= $highestRow; $row++) {
            if (!is_int($idColumnIndex) || !is_int($pointsColumnIndex)) {
                continue;
            }
            
            $idColumnLetter = $this->convertColumnIndexToLetter($idColumnIndex);
            $pointsColumnLetter = $this->convertColumnIndexToLetter($pointsColumnIndex);
            $challengeIdColumnLetter = $this->convertColumnIndexToLetter($challengeIdColumnIndex);

            $idValue = $worksheet->getCell($idColumnLetter . $row)->getValue();
            $pointsValue = $worksheet->getCell($pointsColumnLetter . $row)->getCalculatedValue();
            $challengeIdValue = $worksheet->getCell($challengeIdColumnLetter . $row)->getValue();

            if ($idValue === null || $idValue === '') {
                continue;
            }

            if ($challengeIdValue === null || $challengeIdValue === '') {
                continue;
            }
            
            $idString = is_scalar($idValue) ? (string) $idValue : '';

            if ($idString === '') {
                continue;
            }

            if (Uuid::isValid($idString) === false) {
                $missingIds[] = $idString;
                continue;
            }

            $challengeIdString = is_scalar($challengeIdValue) ? (string) $challengeIdValue : '';

            if ($challengeIdString === '') {
                continue;
            }

            if (Uuid::isValid($challengeIdString) === false) {
                continue;
            }

            $challengeIds[$challengeIdString] = $challengeIdString;

            try {
                $playerChallengeAnswer = $this->playerChallengeAnswerRepository->get(Uuid::fromString($idString));
            } catch (PlayerChallengeAnswerNotFound) {
                $missingIds[] = $idString;
                continue;
            }

            $points = is_scalar($pointsValue) ? (int) $pointsValue : 0;
            $playerChallengeAnswer->evaluate($points);
            $importedCount++;
        }

        foreach ($challengeIds as $challengeId) {
            $challenge = $this->challengeRepository->get(Uuid::fromString($challengeId));
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
