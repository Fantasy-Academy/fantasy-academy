<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests;

use FantasyAcademy\API\Services\ProvideIdentity;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Service\ResetInterface;

/**
 * Test-only identity provider that returns predictable UUIDs for deterministic testing.
 * Automatically resets between tests via Symfony's ResettableServicePass.
 */
final class PredictableIdentityProvider implements ProvideIdentity, ResetInterface
{
    /**
     * @var array<int, string>
     */
    private const array PREDEFINED_UUIDS = [
        '01933333-0000-7000-8000-000000000001',
        '01933333-0000-7000-8000-000000000002',
        '01933333-0000-7000-8000-000000000003',
        '01933333-0000-7000-8000-000000000004',
        '01933333-0000-7000-8000-000000000005',
        '01933333-0000-7000-8000-000000000006',
        '01933333-0000-7000-8000-000000000007',
        '01933333-0000-7000-8000-000000000008',
        '01933333-0000-7000-8000-000000000009',
        '01933333-0000-7000-8000-000000000010',
        '01933333-0000-7000-8000-000000000011',
        '01933333-0000-7000-8000-000000000012',
        '01933333-0000-7000-8000-000000000013',
        '01933333-0000-7000-8000-000000000014',
        '01933333-0000-7000-8000-000000000015',
        '01933333-0000-7000-8000-000000000016',
        '01933333-0000-7000-8000-000000000017',
        '01933333-0000-7000-8000-000000000018',
        '01933333-0000-7000-8000-000000000019',
        '01933333-0000-7000-8000-000000000020',
    ];

    private int $currentIndex = 0;

    public function next(): Uuid
    {
        if ($this->currentIndex >= count(self::PREDEFINED_UUIDS)) {
            throw new \RuntimeException('Exhausted all predefined UUIDs in test. Increase PREDEFINED_UUIDS array size.');
        }

        $uuid = Uuid::fromString(self::PREDEFINED_UUIDS[$this->currentIndex]);
        $this->currentIndex++;

        return $uuid;
    }

    public function reset(): void
    {
        $this->currentIndex = 0;
    }
}
