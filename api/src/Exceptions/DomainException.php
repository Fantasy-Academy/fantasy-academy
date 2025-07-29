<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Exceptions;

use Symfony\Contracts\Translation\TranslatorInterface;

interface DomainException extends \Throwable
{
    public function statusCode(): int;

    public function toHumanReadableMessage(TranslatorInterface $translator): string;
}
