<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services\Import;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

readonly final class SpreadsheetReader
{
    /**
     * Reads a worksheet into an array of associative rows using the first row as headers.
     * Values are taken as displayed text (strings), preserving nulls for empty cells.
     * Empty rows are skipped.
     *
     * @return list<array<string, mixed>>
     */
    public function readSheetAsAssoc(Worksheet $worksheet): array
    {
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();

        // Build header map from row 1
        $headerRow = $worksheet->rangeToArray("A1:{$highestColumn}1", null, true, true, true)[1] ?? [];
        $headers = [];

        foreach ($headerRow as $col => $value) {
            /** @var null|string $value */
            $headers[$col] = $this->normalizeHeader((string) $value);
        }

        $rows = [];

        for ($r = 2; $r <= $highestRow; $r++) {
            $rowCells = $worksheet->rangeToArray("A{$r}:{$highestColumn}{$r}", null, true, true, true)[$r] ?? [];
            $assoc = [];
            $allEmpty = true;

            foreach ($rowCells as $col => $value) {
                /** @var string $key */
                $key = $headers[$col] ?? (string) $col;

                // With formatData=true (rangeToArray) and readDataOnly=false, $value is the
                // displayed text from Excel. Keep it as-is (string) and preserve nulls.
                if ($value !== null && $value !== '') {
                    $allEmpty = false;
                }

                $assoc[$key] = $value;
            }

            if (!$allEmpty) {
                $rows[] = $assoc;
            }
        }

        return $rows;
    }

    /**
     * Normalizes header names to snake_case alphanumeric keys.
     */
    public function normalizeHeader(string $raw): string
    {
        $k = strtolower(trim($raw));
        $k = preg_replace('/\s+/', '_', $k) ?? '';
        $k = preg_replace('/[^a-z0-9_]/', '', $k) ?? '';

        return $k !== '' ? $k : 'col';
    }
}
