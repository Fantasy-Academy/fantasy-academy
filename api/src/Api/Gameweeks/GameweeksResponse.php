<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\Gameweeks;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;

#[ApiResource(
    shortName: 'Gameweeks',
)]
#[Get(
    uriTemplate: '/gameweeks',
    provider: GameweeksProvider::class,
)]
readonly final class GameweeksResponse
{
    public function __construct(
        public null|GameweekResponse $current,
        public null|GameweekResponse $next,
        public null|GameweekResponse $previous,
    ) {
    }
}
