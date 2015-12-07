<?php

namespace RP\DevBundle\Test\Phpunit\Extension;

use Symfony\Component\DependencyInjection\Container;

/**
 * @property Container $container
 */
trait TransactionExtensionTrait
{
    protected function startTransaction()
    {
        /** @var $em \Doctrine\ORM\EntityManager */
        foreach (static::$container->get('doctrine')->getManagers() as $em) {
            $em->clear();
            $em->getConnection()->beginTransaction();
        }
    }

    protected function rollbackTransaction()
    {
        //the error can be thrown during setUp
        //It would be caught by phpunit and tearDown called.
        //In this case we could not rollback since container may not exist.
        if (false == static::$container) {
            return;
        }

        /** @var $em \Doctrine\ORM\EntityManager */
        foreach (static::$container->get('doctrine')->getManagers() as $em) {
            $connection = $em->getConnection();

            while ($connection->isTransactionActive()) {
                $connection->rollback();
            }
        }
    }
}
