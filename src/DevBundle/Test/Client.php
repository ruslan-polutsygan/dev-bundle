<?php

namespace RP\DevBundle\Test;

use Symfony\Bundle\FrameworkBundle\Client as BaseClient;

class Client extends BaseClient
{
    public function ajaxRequest($method, $uri, array $parameters = [], array $files = [], array $server = [], $content = null, $changeHistory = true)
    {
        $server = ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'] + $server;

        parent::request($method, $uri, $parameters, $files, $server, $content, $changeHistory);
    }
}
