<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Exceptions;

use Exception;

final class ImportResultsWarning extends Exception
{
    /**
     * @param array<string> $missingIds
     */
    public function __construct(
        public readonly array $missingIds,
        public readonly int $importedCount,
    ) {
        $message = sprintf(
            '%d results imported successfully. %d IDs not found: %s',
            $importedCount,
            count($missingIds),
            implode(', ', $missingIds)
        );
        
        parent::__construct($message);
    }
}