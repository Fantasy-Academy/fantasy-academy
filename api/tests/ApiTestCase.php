<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase as BaseApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;

/**
 * Base test case for API tests with default headers configured
 */
abstract class ApiTestCase extends BaseApiTestCase
{
    /**
     * Create a client with default JSON headers
     *
     * @param array<string, mixed> $options
     * @param array<string, mixed> $defaultOptions
     */
    protected static function createClient(array $options = [], array $defaultOptions = []): Client
    {
        // Merge with default headers
        $defaultOptions = array_merge_recursive([
            'headers' => [
                'Accept' => 'application/json',
            ],
        ], $defaultOptions);

        return parent::createClient($options, $defaultOptions);
    }
}
