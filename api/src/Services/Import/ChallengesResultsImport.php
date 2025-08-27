<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services\Import;

use Doctrine\ORM\EntityManagerInterface;
use FantasyAcademy\API\Entity\PlayerChallengeAnswer;
use FantasyAcademy\API\Exceptions\ImportFailed;
use FantasyAcademy\API\Exceptions\ImportResultsWarning;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;
use Throwable;

readonly final class ChallengesResultsImport
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

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
        
        if ($idColumnIndex === false) {
            throw new ImportFailed('Column "id" not found in the Points sheet.');
        }
        
        if ($pointsColumnIndex === false) {
            throw new ImportFailed('Column "points" not found in the Points sheet.');
        }
        
        $missingIds = [];
        $importedCount = 0;
        
        for ($row = 2; $row <= $highestRow; $row++) {
            if (!is_int($idColumnIndex) || !is_int($pointsColumnIndex)) {
                continue;
            }
            
            $idColumnLetter = chr(65 + $idColumnIndex);
            $pointsColumnLetter = chr(65 + $pointsColumnIndex);
            
            $idValue = $worksheet->getCell($idColumnLetter . $row)->getValue();
            $pointsValue = $worksheet->getCell($pointsColumnLetter . $row)->getValue();
            
            if ($idValue === null || $idValue === '') {
                continue;
            }
            
            try {
                $idString = is_scalar($idValue) ? (string) $idValue : '';
                if ($idString === '') {
                    continue;
                }
                
                $uuid = Uuid::fromString($idString);
                $playerChallengeAnswer = $this->entityManager->find(PlayerChallengeAnswer::class, $uuid);
                
                if ($playerChallengeAnswer === null) {
                    $missingIds[] = $idString;
                    continue;
                }
                
                $points = is_scalar($pointsValue) ? (int) $pointsValue : 0;
                $playerChallengeAnswer->evaluate($points);
                $importedCount++;
                
            } catch (Throwable) {
                $idString = is_scalar($idValue) ? (string) $idValue : 'invalid';
                $missingIds[] = $idString;
            }
        }
        
        $this->entityManager->flush();
        
        if (!empty($missingIds)) {
            throw new ImportResultsWarning($missingIds, $importedCount);
        }
    }
}
