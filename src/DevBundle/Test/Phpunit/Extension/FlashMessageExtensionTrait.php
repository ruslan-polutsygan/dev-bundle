<?php

namespace RP\DevBundle\Test\Phpunit\Extension;

use Symfony\Component\DomCrawler\Crawler;

trait FlashMessageExtensionTrait
{
    public function assertSuccessFlashMessage($message)
    {
        $crawler = new Crawler();
        $crawler->addContent(static::$client->getResponse()->getContent());

        $this->assertEqualsTrimmed($message, $crawler->filter('.flashes .alert.alert-success')->text());
    }

    public function assertErrorFlashMessage($message)
    {
        $crawler = new Crawler();
        $crawler->addContent(static::$client->getResponse()->getContent());

        $this->assertEqualsTrimmed($message, $crawler->filter('.flashes .alert.alert-danger')->text());
    }

    public function assertWarningFlashMessage($message)
    {
        $crawler = new Crawler();
        $crawler->addContent(static::$client->getResponse()->getContent());

        $this->assertEqualsTrimmed($message, $crawler->filter('.flashes .alert.alert-warning')->text());
    }
}
