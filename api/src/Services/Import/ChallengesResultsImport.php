<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services\Import;

use Doctrine\ORM\EntityManagerInterface;
use FantasyAcademy\API\Entity\Challenge;
use FantasyAcademy\API\Entity\PlayerChallengeAnswer;
use FantasyAcademy\API\Exceptions\ImportFailed;
use FantasyAcademy\API\Exceptions\ImportResultsWarning;
use FantasyAcademy\API\Exceptions\PlayerChallengeAnswerNotFound;
use FantasyAcademy\API\Repository\PlayerChallengeAnswerRepository;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Psr\Clock\ClockInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

readonly final class ChallengesResultsImport
{
    private const string UUID_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';

    public function __construct(
        private EntityManagerInterface $entityManager,
        private PlayerChallengeAnswerRepository $playerChallengeAnswerRepository,
        private ClockInterface $clock,
        private SpreadsheetReader $spreadsheetReader,
    ) {
    }

    /**
     * @throws ImportResultsWarning
     * @throws ImportFailed
     */
    public function importFile(UploadedFile $file): void
    {
        $worksheet = $this->loadPointsWorksheet($file->getPathname());
        $rows = $this->spreadsheetReader->readSheetAsAssoc($worksheet);

        $this->validateRequiredColumns($rows);

        /** @var array<string, Challenge> $challenges */
        $challenges = [];
        $missingIds = [];
        $importedCount = 0;

        foreach ($rows as $index => $row) {
            $excelRow = $index + 2; // Excel rows start at 2 (row 1 is header)
            $result = $this->processRow($row, $excelRow, $missingIds);

            if ($result === null) {
                continue;
            }

            [$answer, $points, $challenge] = $result;
            $answer->evaluate($points);
            $challenges[$challenge->id->toString()] = $challenge;
            $importedCount++;
        }

        $this->evaluateChallenges($challenges);
        $this->entityManager->flush();

        if (!empty($missingIds)) {
            throw new ImportResultsWarning($missingIds, $importedCount);
        }
    }

    /**
     * @throws ImportFailed
     */
    private function loadPointsWorksheet(string $path): Worksheet
    {
        $spreadsheet = IOFactory::load($path);

        if (!$spreadsheet->sheetNameExists('Points')) {
            throw new ImportFailed('Sheet "Points" not found in the uploaded file.');
        }

        $worksheet = $spreadsheet->getSheetByName('Points');

        if ($worksheet === null) {
            throw new ImportFailed('Could not access the "Points" sheet.');
        }

        return $worksheet;
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @throws ImportFailed
     */
    private function validateRequiredColumns(array $rows): void
    {
        if (empty($rows)) {
            return;
        }

        $firstRow = $rows[0];

        if (!array_key_exists('id', $firstRow)) {
            throw new ImportFailed('Column "id" not found in the Points sheet.');
        }

        if (!array_key_exists('points', $firstRow)) {
            throw new ImportFailed('Column "points" not found in the Points sheet.');
        }
    }

    /**
     * @param array<string, mixed> $row
     * @param array<string> $missingIds
     * @return null|array{PlayerChallengeAnswer, int, Challenge}
     */
    private function processRow(array $row, int $excelRow, array &$missingIds): ?array
    {
        $idValue = $this->extractStringValue($row['id'] ?? null);

        if ($idValue === '') {
            return null;
        }

        if (!preg_match(self::UUID_PATTERN, $idValue)) {
            $missingIds[] = sprintf('Row %d: %s', $excelRow, $idValue);
            return null;
        }

        $playerChallengeAnswerId = Uuid::fromString($idValue);

        try {
            $playerChallengeAnswer = $this->playerChallengeAnswerRepository->get($playerChallengeAnswerId);
        } catch (PlayerChallengeAnswerNotFound) {
            $missingIds[] = sprintf('Row %d: %s', $excelRow, $idValue);
            return null;
        }

        $points = $this->extractIntValue($row['points'] ?? null);

        return [$playerChallengeAnswer, $points, $playerChallengeAnswer->challenge];
    }

    /**
     * @param array<string, Challenge> $challenges
     */
    private function evaluateChallenges(array $challenges): void
    {
        foreach ($challenges as $challenge) {
            $challenge->evaluate($this->clock->now());
        }
    }

    private function extractStringValue(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return is_scalar($value) ? trim((string) $value) : '';
    }

    private function extractIntValue(mixed $value): int
    {
        return is_scalar($value) ? (int) $value : 0;
    }
}
