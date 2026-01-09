<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services\Stripe\Value;

readonly final class PortalSessionResult
{
    public function __construct(
        public string $url,
    ) {
    }
}
