<?php

namespace Evotodi\PasswordMeterBundle\Tests;

use PHPUnit\Framework\TestCase;

class CustomTestCase extends TestCase
{
    public static function callMethod($obj, $name, array $args) {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }
}