<?php

namespace RP\DevBundle\Test\Phpunit;

use RP\DevBundle\Test\Client;
use RP\DevBundle\Test\Phpunit\Extension\FixturesExtensionTrait;
use RP\DevBundle\Test\Phpunit\Extension\TransactionExtensionTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class DatabaseTestCase extends BaseWebTestCase
{
    use FixturesExtensionTrait;
    use TransactionExtensionTrait;

    /**
     * @var Client
     */
    protected static $client;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected static $container;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        static::$client = static::createClient();
        static::$container = static::$kernel->getContainer();

        $this->setUpFixtures();
        $this->startTransaction();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->rollbackTransaction();

        BaseWebTestCase::tearDown();
    }

    /**
     * @return Client
     */
    protected function getClient()
    {
        return static::$client;
    }

    /**
     * @return KernelInterface
     */
    protected function getKernel()
    {
        return static::$kernel;
    }

    protected function getEntityManager()
    {
        return static::$container->get('doctrine.orm.default_entity_manager');
    }

    protected function refreshFixture(&$object)
    {
        $object = $this->getEntityManager()->getRepository(get_class($object))->find($object->getId());
    }

    protected function getContainer()
    {
        return static::$container;
    }
}
