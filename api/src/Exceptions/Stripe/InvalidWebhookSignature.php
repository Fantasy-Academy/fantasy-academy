<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Exceptions\Stripe;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[WithHttpStatus(Response::HTTP_BAD_REQUEST)]
final class InvalidWebhookSignature extends \RuntimeException
{
}
