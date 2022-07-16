<?php

namespace App\Tests\Functional\SymfonyPanther;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\PantherTestCase;

class BasePantherTestCase extends PantherTestCase
{

    protected function takeScreenshot(Client $client, string $filename): void
    {
        $client->takeScreenshot("var/tests/screenshots/{$filename}.png");
    }

    protected function initSeleniumClient(): Client
    {
        static::createPantherClient();
        static::startWebServer();

        $capabilities = $this->getChromeCapabilities();
        $client = Client::createSeleniumClient('http://127.0.0.1:4444/wd/hub', $capabilities, 'http://127.0.0.1:9080');

        return $client;
    }


    private function getChromeCapabilities(): DesiredCapabilities
    {
        $chromeOptions = $this->getChromeOptions();
        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $chromeOptions);

        return $capabilities;
    }

    private function getChromeOptions(): ChromeOptions
    {
        $chromeOptions = new ChromeOptions();
        $chromeOptions->addArguments([
            '--window-size=1920,1080',
            // '--headless',
            '--no-sandbox',
            '--disable-dev-shm-usage',
        ]);

        return $chromeOptions;
    }
}
