<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Exceptions;

use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

final class UserAlreadyRegistered extends UnrecoverableMessageHandlingException
{
}
