<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Exceptions;

use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Contracts\Translation\TranslatorInterface;

final class NotEnoughChoices extends UnrecoverableMessageHandlingException implements DomainException
{
    public function __construct(
        readonly private int $count,
    ) {
        parent::__construct();
    }

    public function statusCode(): int
    {
        return 422;
    }

    public function toHumanReadableMessage(TranslatorInterface $translator): string
    {
        return $translator->trans('not_enough_choices', parameters: ['%count%' => $this->count], domain: 'exceptions');
    }
}
