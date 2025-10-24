<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\API;

use FantasyAcademy\API\Tests\DataFixtures\UserFixture;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LoginTest extends WebTestCase
{
    public function testUserCanLoginWithValidCredentials(): void
    {
        $client = self::createClient();

        $client->jsonRequest(
            'POST',
            '/api/login',
            [
                'email' => UserFixture::USER_2_EMAIL,
                'password' => UserFixture::USER_PASSWORD,
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $responseData = json_decode($client->getResponse()->getContent(), true);

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

        $client->jsonRequest(
            'POST',
            '/api/login',
            [
                'email' => UserFixture::USER_2_EMAIL,
                'password' => 'wrong-password',
            ]
        );

        $this->assertResponseStatusCodeSame(401);

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('message', $responseData);
    }

    public function testLoginFailsWithNonExistentUser(): void
    {
        $client = self::createClient();

        $client->jsonRequest(
            'POST',
            '/api/login',
            [
                'email' => 'nonexistent@example.com',
                'password' => 'any-password',
            ]
        );

        $this->assertResponseStatusCodeSame(401);

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('message', $responseData);
    }

    public function testLoginFailsWithMissingEmail(): void
    {
        $client = self::createClient();

        $client->jsonRequest(
            'POST',
            '/api/login',
            [
                'password' => UserFixture::USER_PASSWORD,
            ]
        );

        $this->assertResponseStatusCodeSame(400);
    }

    public function testLoginFailsWithMissingPassword(): void
    {
        $client = self::createClient();

        $client->jsonRequest(
            'POST',
            '/api/login',
            [
                'email' => UserFixture::USER_2_EMAIL,
            ]
        );

        $this->assertResponseStatusCodeSame(400);
    }
}
