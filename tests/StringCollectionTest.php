<?php

namespace Evotodi\PasswordMeterBundle\Tests;

use Evotodi\PasswordMeterBundle\Models\StringCollection;
use PHPUnit\Framework\TestCase;

class StringCollectionTest extends TestCase
{
    private StringCollection $stringCollection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stringCollection = new StringCollection();
    }

    public function testValueException(): void
    {
        self::expectException(\TypeError::class);
        $this->stringCollection['a'] = 1;
    }

    public function testCollectionKeys(): void
    {
        $this->stringCollection['a'] = 'aaa';
        $this->stringCollection['b'] = 'bbb';

        self::assertNotContains('a', $this->stringCollection->toArray());
    }
}