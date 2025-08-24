<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Exceptions;

use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PasswordResetTokenExpired extends UnrecoverableMessageHandlingException implements DomainException
{
    public function statusCode(): int
    {
        return 410;
    }

    public function toHumanReadableMessage(TranslatorInterface $translator): string
    {
        return $translator->trans('password_reset_token_expired', domain: 'exceptions');
    }
}