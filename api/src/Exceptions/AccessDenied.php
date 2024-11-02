<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

#[WithHttpStatus(Response::HTTP_FORBIDDEN)]
final class AccessDenied extends UnrecoverableMessageHandlingException
{
}
