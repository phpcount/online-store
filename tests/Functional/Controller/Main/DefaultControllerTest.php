<?php

namespace App\Tests\Functional\Controller\Main;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group functional
 */
class DefaultControllerTest extends WebTestCase
{
    public function testRedirectEmptyUrlToLocale(): void
    {
        $url = '/';
        $client = static::createClient();
        $crawler = $client->request('GET', $url);

        // $this->assertResponseIsSuccessful();

        $this->assertResponseRedirects(
            'http://localhost/en',
            Response::HTTP_MOVED_PERMANENTLY,
            sprintf('The %s URL redirects to the version with locale', $url)
        );
    }

    /**
     * @dataProvider getPublicUrls
     *
     * @param string $url
     * @return void
     */
    public function testPublicUrls(string $url): void
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertResponseIsSuccessful(
            sprintf("The %s public URL loads correctly", $url)
        );
    }

    /**
     * @dataProvider getSecurityUrls
     *
     * @param string $url
     * @return void
     */
    public function testSecurityUrls(string $url): void
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertResponseRedirects(
            '/en/login',
            Response::HTTP_FOUND,
            sprintf('The %s URL redirects to the login page', $url)
        );
    }

    public function getPublicUrls(): ?\Generator
    {
        $urls = [
            '/en/',
            '/en/login',
            '/en/registration',
            '/en/reset-password',
        ];

        return $this->makeGenerator($urls);
    }

    public function getSecurityUrls(): ?\Generator
    {
        $urls = [
            '/en/profile',
            '/en/profile/edit',
        ];

        return $this->makeGenerator($urls);
    }

    private function makeGenerator(array $items) : ?\Generator
    {
        foreach ($items as $item) {
            yield [$item];
        }
    }
}
