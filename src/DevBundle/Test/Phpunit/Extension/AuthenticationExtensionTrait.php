<?php

namespace RP\DevBundle\Test\Phpunit\Extension;

trait AuthenticationExtensionTrait
{
    public function authenticateAs($username)
    {
        static::$client->setServerParameter('HTTP_X_TEST_AUTH_USERNAME', $username);
    }
    public function authenticateAsAdmin()
    {
        static::$client->setServerParameter('HTTP_X_TEST_AUTH_USERNAME', 'admin');
    }

    public function authenticateAsUser()
    {
        static::$client->setServerParameter('HTTP_X_TEST_AUTH_USERNAME', 'user');
    }
}
