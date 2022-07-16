<?php

namespace App\Tests\Functional\ApiPlatform;

use App\Repository\UserRepository;
use App\Tests\TestUtils\Fixtures\UserFixtures;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Response;

class ResourceTestUtils extends WebTestCase
{
    protected const REQUEST_HEADER = [
        'HTTP_ACCEPT' => 'application/ld+json',
        'CONTENT_TYPE' => 'application/json',
    ];

    protected const REQUEST_HEADER_PATCH = [
        'HTTP_ACCEPT' => 'application/ld+json',
        'CONTENT_TYPE' => 'application/merge-patch+json',
    ];

    /**
     *
     * @var string
     */
    protected $uriKey = '';


    protected function printResponsedContent(AbstractBrowser $client, $var)
    {
        dump($var, json_decode($client->getResponse()->getContent(), true));
    }

    protected function checkDefaultUserhasNotAccess(KernelBrowser $client, string $uri, string $metod): void
    {
        /** @var UserRepository $userRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(["email" => UserFixtures::USER_1_EMAIL]);

        $client->loginUser($user, 'main');

        $client->request($metod, $uri, [], [], self::REQUEST_HEADER, json_encode([]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

}
