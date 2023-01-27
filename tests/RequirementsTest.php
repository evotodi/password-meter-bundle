<?php

namespace Evotodi\PasswordMeterBundle\Tests;

use Evotodi\PasswordMeterBundle\Models\Requirements;
use Evotodi\PasswordMeterBundle\Models\Message;
use Evotodi\PasswordMeterBundle\Models\StringCollection;
use PHPUnit\Framework\TestCase;

class RequirementsTest extends TestCase
{
    private Requirements $config;

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = new Requirements();
    }

    public function testSetMinLengthException(): void
    {
        self::expectException(\ValueError::class);
        $this->config->setMinLength(0);
    }

    public function testSetMaxLengthException(): void
    {
        $this->config->setMinLength(10);

        self::expectException(\ValueError::class);
        $this->config->setMaxLength(1);
    }

    public function testSetUniqueLettersMinLengthException(): void
    {
        self::expectException(\ValueError::class);
        $this->config->setUniqueLettersMinLength(0);
    }

    public function testSetUppercaseLettersMinLengthException(): void
    {
        self::expectException(\ValueError::class);
        $this->config->setUppercaseLettersMinLength(0);
    }

    public function testSetLowercaseLettersMinLengthException(): void
    {
        self::expectException(\ValueError::class);
        $this->config->setLowercaseLettersMinLength(0);
    }

    public function testSetNumbersMinLengthException(): void
    {
        self::expectException(\ValueError::class);
        $this->config->setNumbersMinLength(0);
    }

    public function testSetSymbolsMinLengthException(): void
    {
        self::expectException(\ValueError::class);
        $this->config->setSymbolsMinLength(0);
    }

    public function testSetIncludeException(): void
    {
        self::expectException(\TypeError::class);
        $this->config->setInclude(new Message('a', 'none'));
    }

    public function testSetExcludeException(): void
    {
        self::expectException(\TypeError::class);
        $this->config->setExclude(new Message('a', 'none'));
    }

    public function testSetIncludeOneException(): void
    {
        self::expectException(\TypeError::class);
        $this->config->setIncludeOne(new Message('a', 'none'));
    }

    public function testSetBlacklistException(): void
    {
        self::expectException(\TypeError::class);
        $this->config->setBlacklist(new Message('a', 'none'));
    }

    public function testSetStartsWithException(): void
    {
        self::expectException(\TypeError::class);
        $this->config->setStartsWith(new Message(1, 'none'));
    }

    public function testSetEndsWithException(): void
    {
        self::expectException(\TypeError::class);
        $this->config->setEndsWith(new Message(1, 'none'));
    }

    public function testSetInclude(): void
    {
        $this->config->setInclude(new StringCollection(['a', 'b', 'c']));
        self::assertInstanceOf(Message::class, $this->config->getInclude());
        self::assertIsArray($this->config->getInclude()->getValue()->toArray());
        self::assertContains('a', $this->config->getInclude()->getValue());
    }

    public function testSetExclude(): void
    {
        $this->config->setExclude(new StringCollection(['a', 'b', 'c']));
        self::assertInstanceOf(Message::class, $this->config->getExclude());
        self::assertIsArray($this->config->getExclude()->getValue()->toArray());
        self::assertContains('a', $this->config->getExclude()->getValue());
    }

    public function testSetBlacklist(): void
    {
        $this->config->setBlacklist(new StringCollection(['a', 'b', 'c']));
        self::assertInstanceOf(Message::class, $this->config->getBlacklist());
        self::assertIsArray($this->config->getBlacklist()->getValue()->toArray());
        self::assertContains('a', $this->config->getBlacklist()->getValue());
    }

    public function testSetIncludeOne(): void
    {
        $this->config->setIncludeOne(new StringCollection(['a', 'b', 'c']));
        self::assertInstanceOf(Message::class, $this->config->getIncludeOne());
        self::assertIsArray($this->config->getIncludeOne()->getValue()->toArray());
        self::assertContains('a', $this->config->getIncludeOne()->getValue());
    }
}