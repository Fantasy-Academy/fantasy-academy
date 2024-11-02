<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use FantasyAcademy\API\Tests\TestingLogin;

final class DashboardControllerTest extends WebTestCase
{
    public function testAnonymousUserWillBeRedirectedToLogin(): void
    {
        $browser = self::createClient();

        $browser->request('GET', '/');

        $this->assertResponseRedirects('/login');
    }

    public function testResponseIsSuccessful(): void
    {
        $browser = self::createClient();

        TestingLogin::logInAsUser($browser, 'user1@test.cz');

        $browser->request('GET', '/');

        $this->assertResponseIsSuccessful();
    }
}
