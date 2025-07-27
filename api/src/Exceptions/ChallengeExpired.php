<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Exceptions;

use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ChallengeExpired extends UnrecoverableMessageHandlingException implements DomainException
{
    public function statusCode(): int
    {
        return 400;
    }

    public function toHumanReadableMessage(TranslatorInterface $translator): string
    {
        return $translator->trans('challenge_expired', domain: 'exceptions');
    }
}
