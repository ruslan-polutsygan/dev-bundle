<?php

namespace RP\DevBundle\Test\Phpunit\Extension;

trait StringExtensionTrait
{
    /**
     * @param string $expected
     * @param string $actual
     * @param string $message
     */
    public function assertEqualsTrimmed($expected, $actual, $message = '')
    {
        $this->assertEquals(trim($expected), str_replace(' '/* non breaking space! */, '', trim($actual)), $message);
    }

    /**
     * @param string $expected
     * @param string $actual
     * @param string $message
     */
    public function assertNotEqualsTrimmed($expected, $actual, $message = '')
    {
        $this->assertNotEquals(trim($expected), str_replace(' '/* non breaking space! */, '', trim($actual)), $message);
    }

    /**
     * @param string $actual
     * @param string $message
     */
    public function assertEmptyTrimmed($actual, $message = '')
    {
        $this->assertEmpty(trim($actual), $message);
    }
}
