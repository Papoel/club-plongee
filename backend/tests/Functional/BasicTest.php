<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class BasicTest extends WebTestCase
{
    public function test404(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_NOT_FOUND);
    }
}
