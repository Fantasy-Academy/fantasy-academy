<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Exceptions;

use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

final class ImportFailed extends UnrecoverableMessageHandlingException
{
    public function __construct(
        string $message,
        public readonly ?int $row = null,
        public readonly ?string $column = null,
        ?\Throwable $previous = null,
    ) {
        $contextualMessage = $message;

        if ($row !== null && $column !== null) {
            $contextualMessage = sprintf("Row %d, column '%s': %s", $row, $column, $message);
        } elseif ($row !== null) {
            $contextualMessage = sprintf('Row %d: %s', $row, $message);
        }

        parent::__construct($contextualMessage, 0, $previous);
    }
}
