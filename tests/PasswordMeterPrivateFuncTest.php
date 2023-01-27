<?php

namespace Evotodi\PasswordMeterBundle\Tests;

use Evotodi\PasswordMeterBundle\Interfaces\RequirementsInterface;
use Evotodi\PasswordMeterBundle\Interfaces\ScoreRangeInterface;
use Evotodi\PasswordMeterBundle\Models\Requirements;
use Evotodi\PasswordMeterBundle\Models\Result;
use Evotodi\PasswordMeterBundle\Models\StringCollection;
use Evotodi\PasswordMeterBundle\PasswordMeter;
use function PHPUnit\Framework\assertEquals;

class PasswordMeterPrivateFuncTest extends CustomTestCase
{
    private PasswordMeter $pm;

    protected function setUp(): void
    {
        parent::setUp();

        $requirementsProvider = $this->createMock(RequirementsInterface::class);
        $scoreProvider = $this->createMock(ScoreRangeInterface::class);
        $this->pm = new PasswordMeter($requirementsProvider, $scoreProvider);
    }

    private function method(string $func, array|string $args): mixed
    {
        if (is_string($args)) {
            $args = [$args];
        }
        return self::callMethod($this->pm, $func, $args);
    }

    private function makeRequirementsProvider(Requirements $requirements): RequirementsInterface
    {
        return new class($requirements) implements RequirementsInterface
        {
            public function __construct(private Requirements $requirements)
            {
            }

            public function getRequirements(): Requirements
            {
                return $this->requirements;
            }
        };
    }

    private function makeScoreProvider(array $scores): ScoreRangeInterface
    {
        return new class($scores) implements ScoreRangeInterface
        {
            public function __construct(private array $scores)
            {
            }

            public function getScoreRange(): array
            {
                return $this->scores;
            }
        };
    }

    public function testChunkString(): void
    {
        $ret = $this->method('chunkString', ['12345', 5]);
        self::assertCount(1, $ret);

        $ret = $this->method('chunkString', ['1234567890', 5]);
        self::assertCount(2, $ret);

        $ret = $this->method('chunkString', ['1234567890', 1]);
        self::assertCount(10, $ret);
    }

    public function testDoesNotContains(): void
    {
        $ret = $this->method('doesNotContains', ['abc', ['d', 'e'], false]);
        self::assertTrue($ret);

        $ret = $this->method('doesNotContains', ['abc', ['a', 'b'], false]);
        self::assertFalse($ret);
    }

    public function testContains(): void
    {
        $ret = $this->method('contains', ['abc', ['a', 'b'], false]);
        self::assertTrue($ret);

        $ret = $this->method('contains', ['abc', ['a', 'd'], false]);
        self::assertFalse($ret);
    }

    public function testContainsOne(): void
    {
        $ret = $this->method('containsOne', ['abc', ['a', 'd'], false]);
        self::assertTrue($ret);

        $ret = $this->method('containsOne', ['abc', ['d', 'e'], false]);
        self::assertFalse($ret);
    }

    public function testIsInBlacklist(): void
    {
        $ret = $this->method('isInBlacklist', ['abc', ['abc', 'def'], false]);
        self::assertTrue($ret);

        $ret = $this->method('isInBlacklist', ['abc', ['def', 'ghi'], false]);
        self::assertFalse($ret);
    }

    public function testBetween(): void
    {
        $ret = $this->method('between', [10, 1, 100]);
        self::assertTrue($ret);

        $ret = $this->method('between', [1000, 1, 100]);
        self::assertFalse($ret);
    }

    public function testIsNumber(): void
    {
        $ret = $this->method('isNumber', '1');
        self::assertTrue($ret);

        $ret = $this->method('isNumber', 'a');
        self::assertFalse($ret);
    }

    public function testIsLetter(): void
    {
        $ret = $this->method('isLetter', 'a');
        self::assertTrue($ret);

        $ret = $this->method('isLetter', '1');
        self::assertFalse($ret);
    }

    public function testIsUppercaseLetter(): void
    {
        $ret = $this->method('isUppercaseLetter', 'A');
        self::assertTrue($ret);

        $ret = $this->method('isUppercaseLetter', 'a');
        self::assertFalse($ret);
    }

    public function testIsLowercaseLetter(): void
    {
        $ret = $this->method('isLowercaseLetter', 'a');
        self::assertTrue($ret);

        $ret = $this->method('isLowercaseLetter', 'A');
        self::assertFalse($ret);
    }

    public function testIsSymbol(): void
    {
        $ret = $this->method('isSymbol', '*');
        self::assertTrue($ret);

        $ret = $this->method('isSymbol', 'a');
        self::assertFalse($ret);
    }

    public function testGetSymbols(): void
    {
        $ret = $this->method('getSymbols', 'a*b%c^');
        self::assertEquals('*%^', $ret);
    }

    public function testGetUppercaseLettersScore(): void
    {
        $ret = $this->method('getUppercaseLettersScore', 'test');
        assertEquals(0, $ret);

        $ret = $this->method('getUppercaseLettersScore', 'Test');
        assertEquals(36, $ret);
    }

    public function testGetLowercaseLettersScore(): void
    {
        $ret = $this->method('getLowercaseLettersScore', 'TEST');
        assertEquals(0, $ret);

        $ret = $this->method('getLowercaseLettersScore', 'Test');
        assertEquals(2, $ret);
    }

    public function testGetNumbersScore(): void
    {
        $ret = $this->method('getNumbersScore', 'TEST');
        assertEquals(0, $ret);

        $ret = $this->method('getNumbersScore', 'Test1');
        assertEquals(16, $ret);
    }

    public function testGetSymbolsScore(): void
    {
        $ret = $this->method('getSymbolsScore', 'TEST');
        assertEquals(0, $ret);

        $ret = $this->method('getSymbolsScore', 'Test%');
        assertEquals(24, $ret);
    }

    public function testGetLettersOnlyScore(): void
    {
        $ret = $this->method('getLettersOnlyScore', 'TEST');
        assertEquals(-4, $ret);

        $ret = $this->method('getLettersOnlyScore', 'Test1234');
        assertEquals(0, $ret);
    }

    public function testGetNumbersOnlyScore(): void
    {
        $ret = $this->method('getNumbersOnlyScore', '1234');
        assertEquals(-4, $ret);

        $ret = $this->method('getNumbersOnlyScore', 'Test1234');
        assertEquals(0, $ret);
    }

    public function testGetConsecutiveUppercaseLettersScore(): void
    {
        $ret = $this->method('getConsecutiveUppercaseLettersScore', 'abcd');
        assertEquals(0, $ret);

        $ret = $this->method('getConsecutiveUppercaseLettersScore', 'AA bb cc');
        assertEquals(-2, $ret);
    }

    public function testGetConsecutiveLowercaseLettersScore(): void
    {
        $ret = $this->method('getConsecutiveLowercaseLettersScore', 'ABCD');
        assertEquals(0, $ret);

        $ret = $this->method('getConsecutiveLowercaseLettersScore', 'AA BB cc');
        assertEquals(-2, $ret);
    }

    public function testGetConsecutiveNumbersScore(): void
    {
        $ret = $this->method('getConsecutiveNumbersScore', 'AbCd');
        assertEquals(0, $ret);

        $ret = $this->method('getConsecutiveNumbersScore', '12 aa BB');
        assertEquals(-2, $ret);
    }

    public function testSortByLength(): void
    {
        $ret = $this->method('sortByLength', [['a', 'ccc', 'bb', 'dddd'], 2]);

        self::assertCount(3, $ret);
        self::assertContains('ccc', $ret);
        self::assertContains('bb', $ret);
        self::assertContains('dddd', $ret);
        self::assertEquals('dddd', $ret[0]);
        self::assertEquals('bb', $ret[2]);
        self::assertEquals('ccc', $ret[1]);

        $ret = $this->method('sortByLength', [['a', 'ccc', 'bb', 'dddd']]);
        self::assertCount(4, $ret);
    }

    public function testSequentialBuilder(): void
    {
        $ret = $this->method('sequentialBuilder', [PasswordMeter::UPPERCASE_LETTERS, PasswordMeter::SEQUENTIAL_MIN_CHUNK]);
        self::assertCount(598, $ret);
    }

    public function testGetSequentialLettersScore(): void
    {
        $ret = $this->method('getSequentialLettersScore', 'abc123');
        assertEquals(-3, $ret);

        $ret = $this->method('getSequentialLettersScore', 'ABCdef123');
        assertEquals(-6, $ret);
    }

    public function testGetSequentialNumbersScore(): void
    {
        $ret = $this->method('getSequentialNumbersScore', 'abc');
        assertEquals(0, $ret);

        $ret = $this->method('getSequentialNumbersScore', 'ABCdef123');
        assertEquals(-3, $ret);
    }

    public function testGetSequentialSymbolsScore(): void
    {
        $ret = $this->method('getSequentialSymbolsScore', 'aabcd');
        assertEquals(0, $ret);

        $ret = $this->method('getSequentialSymbolsScore', 'ABCdef123#$%&^');
        assertEquals(-6, $ret);
    }

    public function testGetRepeatCharactersScore(): void
    {
        $ret = $this->method('getRepeatCharactersScore', 'aaaaaaaaAAAbbcd');
        self::assertEquals(-25, $ret);
    }

    /**
     * @dataProvider provideRequirements
     */
    public function testGetRequirementsScore(YieldReq $yieldReq): void
    {

        $requirementsProvider = $this->makeRequirementsProvider($yieldReq->requirements);
        $scoreProvider = $this->createMock(ScoreRangeInterface::class);
        $pm = new PasswordMeter($requirementsProvider, $scoreProvider);

        $ret = self::callMethod($pm, 'getRequirementsScore', [$yieldReq->password, false]);
        self::assertEquals($yieldReq->count, count($ret));
        self::assertEquals($yieldReq->expect, $ret);
    }

    private function provideRequirements()
    {
        yield 'Min Length' => [new YieldReq(password: 'aaaaaaaaAAAbbcd', requirements: new Requirements(minLength: 30), count: 1, expect: ['The minimum password length is 30.'])];
        yield 'Max Length' => [new YieldReq(password: 'aaaaaaaaAAAbbcd', requirements: new Requirements(maxLength: 3), count: 1, expect: ['The maximum password length is 3.'])];
        yield 'Uppercase Min Length' => [new YieldReq(password: 'aaaaaaaaAAAbbcd', requirements: new Requirements(uppercaseLettersMinLength: 6), count: 1, expect: ['You must use at least 6 uppercase letter(s).'])];
        yield 'Lowercase Min Length' => [new YieldReq(password: 'aAAAd', requirements: new Requirements(lowercaseLettersMinLength: 6), count: 1, expect: ['You must use at least 6 lowercase letter(s).'])];
        yield 'Numbers Min Length' => [new YieldReq(password: 'aAAAd', requirements: new Requirements(numbersMinLength: 1), count: 1, expect: ['You must use at least 1 number(s).'])];
        yield 'Symbols Min Length' => [new YieldReq(password: 'aAAAd', requirements: new Requirements(symbolsMinLength: 1), count: 1, expect: ['You must use at least 1 symbol(s).'])];
        yield 'Unique Min Length' => [new YieldReq(password: 'aAAAd', requirements: new Requirements(uniqueLettersMinLength: 4), count: 1, expect: ['You must use at least 4 unique letter(s).'])];
        yield 'Starts With' => [new YieldReq(password: 'aAAAd', requirements: new Requirements(startsWith: 'hello'), count: 1, expect: ['The password must start with "hello".'])];
        yield 'Ends With' => [new YieldReq(password: 'aAAAd', requirements: new Requirements(endsWith: 'hello'), count: 1, expect: ['The password must end with "hello".'])];
        yield 'Include' => [new YieldReq(password: 'aAAAd', requirements: new Requirements(include: new StringCollection(['hello'])), count: 1, expect: ['The Password must include [hello].'])];
        yield 'Exclude' => [new YieldReq(password: 'aAAAdhello', requirements: new Requirements(exclude: new StringCollection(['hello'])), count: 1, expect: ['The Password must exclude [hello].'])];
        yield 'Include One' => [new YieldReq(password: 'aAAAd', requirements: new Requirements(includeOne: new StringCollection(['hello'])), count: 1, expect: ['The Password must include at least one item specified [hello] .'])];
        yield 'Blacklist' => [new YieldReq(password: 'hello', requirements: new Requirements(blacklist: new StringCollection(['hello'])), count: 1, expect: ['Your password is in the blacklist.'])];
    }

    public function testGetResultExceptionShort(): void
    {
        $requirementsProvider = $this->makeRequirementsProvider(new Requirements());
        $scoreProvider = $this->makeScoreProvider([
            '_' => 'veryStrong', // 180 <= x <  200
        ]);
        $pm = new PasswordMeter($requirementsProvider, $scoreProvider);

        self::expectException(\ValueError::class);
        self::expectExceptionMessage('ScoreRangeProvider must provide at least 2 score ranges');
        $ret = self::callMethod($pm, 'getResult', ['asdf', false]);
    }

    public function testGetResultExceptionMissing(): void
    {
        $requirementsProvider = $this->makeRequirementsProvider(new Requirements());
        $scoreProvider = $this->makeScoreProvider([
            '40' => 'veryWeak', // 001 <= x <  040
            '80' => 'weak', // 040 <= x <  080
            '120' => 'medium', // 080 <= x <  120
            '180' => 'strong', // 120 <= x <  180
            '200' => 'veryStrong', // 180 <= x <  200
            'x' => 'perfect', //  >= 200
        ]);
        $pm = new PasswordMeter($requirementsProvider, $scoreProvider);

        self::expectException(\ValueError::class);
        self::expectExceptionMessage("The last key of the array ScoreRangeProvider provides must be '_' ");
        $ret = self::callMethod($pm, 'getResult', ['asdf', false]);
    }

    public function testGetResult(): void
    {
        $requirementsProvider = $this->makeRequirementsProvider(new Requirements());
        $scoreProvider = $this->makeScoreProvider([
            '40' => 'veryWeak', // 001 <= x <  040
            '80' => 'weak', // 040 <= x <  080
            '120' => 'medium', // 080 <= x <  120
            '180' => 'strong', // 120 <= x <  180
            '200' => 'veryStrong', // 180 <= x <  200
            '_' => 'perfect', //  >= 200
        ]);
        $pm = new PasswordMeter($requirementsProvider, $scoreProvider);

        $ret = self::callMethod($pm, 'getResult', ['asdf', false]);

        self::assertInstanceOf(Result::class, $ret);
        self::assertEquals(26, $ret->getScore());
        self::assertEquals('veryWeak', $ret->getStatus());
        self::assertEquals(13.0, $ret->getPercent());
    }

    public function testGetResults(): void
    {
        $requirementsProvider = $this->makeRequirementsProvider(new Requirements());
        $scoreProvider = $this->makeScoreProvider([
            '40' => 'veryWeak', // 001 <= x <  040
            '80' => 'weak', // 040 <= x <  080
            '120' => 'medium', // 080 <= x <  120
            '180' => 'strong', // 120 <= x <  180
            '200' => 'veryStrong', // 180 <= x <  200
            '_' => 'perfect', //  >= 200
        ]);
        $pm = new PasswordMeter($requirementsProvider, $scoreProvider);

        $ret = self::callMethod($pm, 'getResults', [new StringCollection(['asdf', 'Homeware8Ound']), false]);
        self::assertIsArray($ret);
    }
}