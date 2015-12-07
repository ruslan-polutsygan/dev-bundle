<?php

namespace RP\DevBundle\Test\Phpunit\Extension;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

trait ResponseExtensionTrait
{
    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param string                                     $expectedRegExp
     */
    public function assertResponseRegExpRedirection(Response $response, $expectedRegExp)
    {
        $this->assertResponseStatusRedirection($response);
        $this->assertRegExp($expectedRegExp, $response->headers->get('Location'), $this->getMessage($response));
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function assertResponseStatusRedirection(Response $response)
    {
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertGreaterThanOrEqual(300, $response->getStatusCode(), $this->getMessage($response));
        $this->assertLessThan(400, $response->getStatusCode(), $this->getMessage($response));
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param string                                     $expectedUrl
     */
    public function assertResponseRedirection(Response $response, $expectedUrl)
    {
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertResponseStatusRedirection($response);
        $this->assertEquals($expectedUrl, $response->headers->get('Location'));
    }

    /**
     * @param string $expectedUrl
     */
    public function assertClientResponseRedirection($expectedUrl)
    {
        $this->assertResponseRedirection(static::$client->getResponse(), $expectedUrl);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param string                                     $expectedUrl
     */
    public function assertResponseRedirectionStartsWith(Response $response, $expectedUrl)
    {
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertResponseStatusRedirection($response);
        $this->assertStringStartsWith($expectedUrl, $response->headers->get('Location'));
    }

    /**
     * @param string $expectedUrl
     */
    public function assertClientResponseRedirectionStartsWith($expectedUrl)
    {
        $this->assertResponseRedirectionStartsWith(static::$client->getResponse(), $expectedUrl);
    }

    /**
     * @param string $expectedRegExp
     */
    public function assertClientResponseRegExpRedirection($expectedRegExp)
    {
        $this->assertResponseRegExpRedirection(static::$client->getResponse(), $expectedRegExp);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param int                                        $expectedStatus
     */
    public function assertResponseStatus(Response $response, $expectedStatus)
    {
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals($expectedStatus, $response->getStatusCode(), $this->getMessage($response));
    }

    /**
     * @param int $expectedStatus
     */
    public function assertClientResponseStatus($expectedStatus)
    {
        $this->assertResponseStatus(static::$client->getResponse(), $expectedStatus);
    }

    /**
     * @param int[] $expectedStatuses
     */
    public function assertClientResponseStatusIn($expectedStatuses = [])
    {
        $this->assertContains(static::$client->getResponse()->getStatusCode(), $expectedStatuses);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function assertResponseContentHtml(Response $response)
    {
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals('text/html; charset=UTF-8', $response->headers->get('Content-Type'), $this->getMessage($response));
    }

    public function assertClientResponseContentHtml()
    {
        $this->assertResponseContentHtml(static::$client->getResponse());
    }

    public function assertClientResponseContentJson()
    {
        $this->assertResponseContentJson(static::$client->getResponse());
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function assertResponseContentJson(Response $response)
    {
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertNotNull(
            $this->getClientResponseJsonContent($response),
            "Failed to decode content. The content is not valid json: \n\n".$response->getContent()
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return mixed
     */
    public function getResponseJsonContent(Response $response)
    {
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);

        return json_decode($response->getContent(), true);
    }

    /**
     * @return mixed
     */
    public function getClientResponseJsonContent()
    {
        return $this->getResponseJsonContent(static::$client->getResponse());
    }

    /**
     * @param string $expectedUrl
     */
    public function assertAbsoluteResponseRedirection($expectedUrl)
    {
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', static::$client->getResponse());
        $this->assertResponseStatusRedirection(static::$client->getResponse());
        $this->assertEquals(sprintf('http://%s%s',
            static::$client->getRequest()->server->get('SERVER_NAME'),
            $expectedUrl
            ),
            static::$client->getResponse()->headers->get('Location')
        );
    }

    public function assertClientResponseExceptionMessageContains($text)
    {
        $this->assertResponseExceptionMessageContains(static::$client->getResponse(), $text);
    }

    public function assertResponseExceptionMessageContains(Response $response, $text)
    {
        $crawler = new Crawler();
        $crawler->addHtmlContent($response->getContent());

        $content = '';
        if ($crawler->filter('.text-exception h1')->count() > 0) {
            $content = trim($crawler->filter('.text-exception h1')->text());
        }

        $this->assertContains($text, $content);
    }

    public function assertClientResponseContentEquals($expectedContent)
    {
        $this->assertEquals($expectedContent, static::$client->getResponse()->getContent());
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return string
     */
    private function getMessage(Response $response)
    {
        if (500 >= $response->getStatusCode() && $response->getStatusCode() < 600) {
            $crawler = new Crawler();
            $crawler->addHtmlContent($response->getContent());

            if ($crawler->filter('.text-exception h1')->count() > 0) {
                $exceptionMessage = trim($crawler->filter('.text-exception h1')->text());

                $trace = '';
                if ($crawler->filter('#traces-0 li')->count() > 0) {
                    list($trace) = explode("\n", trim($crawler->filter('#traces-0 li')->text()));
                }

                return $message = 'Internal Server Error: '.$exceptionMessage.' '.$trace;
            }
        }

        return $response->getContent();
    }
}
