<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\Subscription;

final class PortalSessionResponse
{
    public function __construct(
        public string $portalUrl,
    ) {
    }
}
