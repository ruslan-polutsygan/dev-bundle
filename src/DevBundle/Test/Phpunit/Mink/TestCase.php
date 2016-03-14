<?php

namespace RP\DevBundle\Test\Phpunit\Mink;

use Behat\Mink\Session;
use Behat\MinkBundle\Test\MinkTestCase as BaseTestCase;
use Symfony\Component\DependencyInjection\Container;

class TestCase extends BaseTestCase
{
    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Session
     */
    protected $session;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        static::bootKernel();

        $this->container = static::$kernel->getContainer();
        $this->baseUrl = $this->container->getParameter('mink.base_url');

        $this->session = $this->getMink()->getSession();
        $this->session->maximizeWindow();
    }

    protected function visit($path)
    {
        $this->session->visit(rtrim($this->baseUrl, '/').'/'.ltrim($path, '/'));

        return $this->session->getPage();
    }

    /**
     * @return Session
     */
    protected function getSession()
    {
        return $this->session;
    }

    /**
     * @return string
     */
    protected function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Clean up Kernel usage in this test.
     */
    protected function tearDown()
    {
        $this->session->reset();

        parent::tearDown();
    }
}
