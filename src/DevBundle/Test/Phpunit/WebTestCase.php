<?php

namespace RP\DevBundle\Test\Phpunit;

use RP\DevBundle\Test\Phpunit\Extension\AuthenticationExtensionTrait;
use RP\DevBundle\Test\Phpunit\Extension\FlashMessageExtensionTrait;
use RP\DevBundle\Test\Phpunit\Extension\ResponseExtensionTrait;
use RP\DevBundle\Test\Phpunit\Extension\StringExtensionTrait;

class WebTestCase extends DatabaseTestCase
{
    use ResponseExtensionTrait;
    use AuthenticationExtensionTrait;
    use StringExtensionTrait;
    use FlashMessageExtensionTrait;
}
