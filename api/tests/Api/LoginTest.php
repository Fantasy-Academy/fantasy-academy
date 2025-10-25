<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use FantasyAcademy\API\Tests\DataFixtures\UserFixture;

final class LoginTest extends ApiTestCase
{
    public function testUserCanLoginWithValidCredentials(): void
    {
        $client = self::createClient();

        $response = $client->request(
            'POST',
            '/api/login',
            [
                'json' => [
                    'email' => UserFixture::USER_2_EMAIL,
                    'password' => UserFixture::USER_PASSWORD,
                ],
            ],
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $responseData = json_decode($response->getContent(), true);

        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('token', $responseData);
        $this->assertIsString($responseData['token']);
        $this->assertNotEmpty($responseData['token']);

        // Verify JWT token structure (should have 3 parts: header.payload.signature)
        $tokenParts = explode('.', $responseData['token']);
        $this->assertCount(3, $tokenParts);
    }

    public function testLoginFailsWithInvalidCredentials(): void
    {
        $client = self::createClient();

        $response = $client->request(
            'POST',
            '/api/login',
            [
                'json' => [
                    'email' => UserFixture::USER_2_EMAIL,
                    'password' => 'wrong-password',
                ],
            ],
        );

        $this->assertResponseStatusCodeSame(401);

        $responseData = json_decode($response->getContent(false), true);

        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('message', $responseData);
    }

    public function testLoginFailsWithNonExistentUser(): void
    {
        $client = self::createClient();

        $response = $client->request(
            'POST',
            '/api/login',
            [
                'json' => [
                    'email' => 'nonexistent@example.com',
                    'password' => 'any-password',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(401);

        $responseData = json_decode($response->getContent(false), true);

        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('message', $responseData);
    }

    public function testLoginFailsWithMissingEmail(): void
    {
        $client = self::createClient();

        $client->request(
            'POST',
            '/api/login',
            [
                'json' => [
                    'password' => UserFixture::USER_PASSWORD,
                ],
            ],
        );

        $this->assertResponseStatusCodeSame(400);
    }

    public function testLoginFailsWithMissingPassword(): void
    {
        $client = self::createClient();

        $client->request(
            'POST',
            '/api/login',
            [
                'json' => [
                    'email' => UserFixture::USER_2_EMAIL,
                ],
            ],
        );

        $this->assertResponseStatusCodeSame(400);
    }
}
