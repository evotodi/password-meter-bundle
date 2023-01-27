<?php

namespace Evotodi\PasswordMeterBundle;

use Evotodi\PasswordMeterBundle\Interfaces\RequirementsInterface;
use Evotodi\PasswordMeterBundle\Interfaces\ScoreRangeInterface;
use Evotodi\PasswordMeterBundle\Models\Requirements;
use Evotodi\PasswordMeterBundle\Models\Message;
use Evotodi\PasswordMeterBundle\Models\Result;
use Evotodi\PasswordMeterBundle\Models\StringCollection;
use function PHPUnit\Framework\arrayHasKey;

class PasswordMeter
{
    public const UPPERCASE_LETTERS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    public const LOWERCASE_LETTERS = 'abcdefghijklmnopqrstuvwxyz';
    public const NUMBERS = '1234567890';
    public const SEQUENTIAL_MIN_CHUNK = 3;
    public const LENGTH_SCORE_RATIO = 9;
    public const UPPERCASE_LETTER_SCORE_RATIO = 12;
    public const LOWERCASE_LETTER_SCORE_RATIO = 2;
    public const NUMBER_SCORE_RATIO = 4;
    public const SYMBOL_SCORE_RATIO = 6;
    public const LETTERS_ONLY_SCORE_RATIO = -1;
    public const NUMBERS_ONLY_SCORE_RATIO = -1;
    public const CONSECUTIVE_UPPERCASE_LETTERS_SCORE_RATIO = -2;
    public const CONSECUTIVE_LOWERCASE_LETTERS_SCORE_RATIO = -2;
    public const CONSECUTIVE_NUMBERS_SCORE_RATIO = -2;
    public const SEQUENTIAL_LETTERS_SCORE_RATIO = -3;
    public const REPEAT_CHARACTERS_SCORE_RATIO_1_5 = -8;
    public const REPEAT_CHARACTERS_SCORE_RATIO_6_10 = -5;
    public const REPEAT_CHARACTERS_SCORE_RATIO_11 = -2;

    public function __construct(private readonly RequirementsInterface $requirements, private readonly ScoreRangeInterface $scoreRange)
    {
    }

    private function replaceKey(array $arr, string $old, string $new): array
    {
        if (array_key_exists($old, $arr)) {
            $keys = array_keys($arr);
            $keys[array_search($old, $keys)] = $new;
            return array_combine($keys, $arr);
        }
        return $arr;
    }

    public function getRequirements(): Requirements
    {
        return $this->requirements->getRequirements();
    }

    private function chunkString(string $str, int $len): array
    {
        $size = ceil(strlen($str) / $len);
        $ret = [];
        $offset = 0;

        for ($i = 0; $i < $size; $i++) {
            $offset = $i * $len;
            $ret[$i] = substr($str, $offset, $offset + $len);
        }

        return $ret;
    }

    private function getLength(?string $text): int
    {
        if (is_null($text)) {
            return 0;
        }

        return strlen($text);
    }

    private function doesNotContains(string $text, array|StringCollection $list = [], bool $ignoreCase = false): bool
    {
        foreach ($list as $item) {
            if ($ignoreCase) {
                if (str_contains(strtolower($text), strtolower($item))) {
                    return false;
                }
            } else {
                if (str_contains($text, $item)) {
                    return false;
                }
            }
        }

        return true;
    }

    private function contains(string $text, array|StringCollection $list = [], bool $ignoreCase = false): bool
    {
        foreach ($list as $item) {
            if ($ignoreCase) {
                if (!str_contains(strtolower($text), strtolower($item))) {
                    return false;
                }
            } else {
                if (!str_contains($text, $item)) {
                    return false;
                }
            }
        }

        return true;
    }

    private function containsOne(string $text, array|StringCollection $list = [], bool $ignoreCase = false): bool
    {
        foreach ($list as $item) {
            if ($ignoreCase) {
                if (str_contains(strtolower($text), strtolower($item))) {
                    return true;
                }
            } else {
                if (str_contains($text, $item)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function isInBlacklist(string $text, array|StringCollection $list = [], bool $ignoreCase = false): bool
    {
        foreach ($list as $item) {
            if ($ignoreCase) {
                if (strtolower($text) == strtolower($item)) {
                    return true;
                }
            } else {
                if ($text == $item) {
                    return true;
                }
            }
        }

        return false;
    }

    private function between(int $x, int $min, int $max): bool
    {
        return $x >= $min and $x < $max;
    }

    private function isNumber(string $text): bool
    {
        if (preg_match('/^\d+$/', $text) === 1) {
            return true;
        }

        return false;
    }

    private function isLetter(string $text): bool
    {
        if (preg_match('/^[a-zA-Z]+$/', $text) === 1) {
            return true;
        }

        return false;
    }

    private function isUppercaseLetter(string $text): bool
    {
        if (preg_match('/^[A-Z]+$/', $text) === 1) {
            return true;
        }

        return false;
    }

    private function isLowercaseLetter(string $text): bool
    {
        if (preg_match('/^[a-z]+$/', $text) === 1) {
            return true;
        }

        return false;
    }

    private function isSymbol(string $text): bool
    {
        return !$this->isNumber($text) and !$this->isLetter($text);
    }

    private function getSymbols(string $text): string|null
    {
        $matches = [];
        preg_match_all('/[^a-zA-Z0-9]/', $text, $matches);
        if (empty($matches)) {
            return null;
        }

        return implode('', $matches[0]);
    }

    private function getLengthScore(string $text): int
    {
        return $this->getLength($text) * self::LENGTH_SCORE_RATIO;
    }

    public function getUppercaseLettersScore(string $text): int
    {
        $n = 0;
        $textArr = str_split($text);
        foreach ($textArr as $letter) {
            if ($this->isUppercaseLetter($letter)) {
                $n++;
            }
        }

        if ($n == 0) {
            return 0;
        }

        return ($this->getLength($text) - $n) * self::UPPERCASE_LETTER_SCORE_RATIO;
    }

    public function getLowercaseLettersScore(string $text): int
    {
        $n = 0;
        $textArr = str_split($text);
        foreach ($textArr as $letter) {
            if ($this->isLowercaseLetter($letter)) {
                $n++;
            }
        }

        if ($n == 0) {
            return 0;
        }

        return ($this->getLength($text) - $n) * self::LOWERCASE_LETTER_SCORE_RATIO;
    }

    public function getNumbersScore(string $text): int
    {
        $n = 0;
        $textArr = str_split($text);
        foreach ($textArr as $letter) {
            if ($this->isNumber($letter)) {
                $n++;
            }
        }

        if ($n == 0) {
            return 0;
        }

        return ($this->getLength($text) - $n) * self::NUMBER_SCORE_RATIO;
    }

    public function getSymbolsScore(string $text): int
    {
        $n = 0;
        $textArr = str_split($text);
        foreach ($textArr as $letter) {
            if ($this->isSymbol($letter)) {
                $n++;
            }
        }

        if ($n == 0) {
            return 0;
        }

        return ($this->getLength($text) - $n) * self::SYMBOL_SCORE_RATIO;
    }

    private function getLettersOnlyScore(string $text): int
    {
        if ($this->isLetter($text)) {
            return $this->getLength($text) * self::LETTERS_ONLY_SCORE_RATIO;
        }

        return 0;
    }

    private function getNumbersOnlyScore(string $text): int
    {
        if ($this->isNumber($text)) {
            return $this->getLength($text) * self::NUMBERS_ONLY_SCORE_RATIO;
        }

        return 0;
    }

    private function getConsecutiveUppercaseLettersScore(string $text): int
    {
        $matches = [];
        $score = 0;

        if (preg_match_all('/[A-Z]+/', $text, $matches) > 0) {
            foreach ($matches[0] as $match) {
                if ($this->getLength($match) > 1) {
                    $score += ($this->getLength($match) - 1) * self::CONSECUTIVE_UPPERCASE_LETTERS_SCORE_RATIO;
                }
            }
        }

        return $score;
    }

    private function getConsecutiveLowercaseLettersScore(string $text): int
    {
        $matches = [];
        $score = 0;
        if (preg_match_all('/[a-z]+/', $text, $matches) > 0) {
            foreach ($matches[0] as $match) {
                if ($this->getLength($match) > 1) {
                    $score += ($this->getLength($match) - 1) * self::CONSECUTIVE_LOWERCASE_LETTERS_SCORE_RATIO;
                }
            }
        }

        return $score;
    }

    private function getConsecutiveNumbersScore(string $text): int
    {
        $matches = [];
        $score = 0;
        if (preg_match_all('/[0-9]+/', $text, $matches) > 0) {
            foreach ($matches[0] as $match) {
                if ($this->getLength($match) > 1) {
                    $score += ($this->getLength($match) - 1) * self::CONSECUTIVE_NUMBERS_SCORE_RATIO;
                }
            }
        }

        return $score;
    }

    private function sortByLength(array $arr, ?int $limit = null): array
    {
        $list = [];

        uasort($arr, function ($a, $b) {
            return strlen($b) - strlen($a);
        });
        $arr = array_values($arr);

        for ($index = 0; $index < count($arr); $index++) {
            if ($limit) {
                if (strlen($arr[$index]) >= $limit) {
                    $list[] = $arr[$index];
                }
            } else {
                $list[] = $arr[$index];
            }
        }

        return $list;
    }

    private function sequentialBuilder(string $text, int $minChunk): array
    {
        $list = [];
        $len = count(str_split($text)) - $minChunk;
        for ($i = 0; $i < $len; $i++) {
            for ($idx = 0; $idx < $len; $idx++) {
                $newText = substr($text, $idx, strlen($text));
                $arr = $this->chunkString($newText, $i + $minChunk);
                foreach ($arr as $a) {
                    $list[] = $a;
                    $list[] = strrev($a);
                }
            }
        }
        $result = array_unique($this->sortByLength($list, $minChunk));

        return array_values($result);
    }

    private function getSequentialLettersScore(string $text): int
    {
        $uStr = $this->sequentialBuilder(self::UPPERCASE_LETTERS, self::SEQUENTIAL_MIN_CHUNK);
        $lStr = $this->sequentialBuilder(self::LOWERCASE_LETTERS, self::SEQUENTIAL_MIN_CHUNK);
        $score = 0;
        $uTxt = $text;
        $lTxt = $text;

        foreach ($uStr as $value) {
            if (str_contains($uTxt, $value)) {
                $score += strlen($value) - (self::SEQUENTIAL_MIN_CHUNK - 1);
                $uTxt = str_replace($value, '', $uTxt);
            }
        }

        foreach ($lStr as $value) {
            if (str_contains($lTxt, $value)) {
                $score += strlen($value) - (self::SEQUENTIAL_MIN_CHUNK - 1);
                $lTxt = str_replace($value, '', $lTxt);
            }
        }

        return $score * self::SEQUENTIAL_LETTERS_SCORE_RATIO;
    }

    private function getSequentialNumbersScore(string $text): int
    {
        $num = $this->sequentialBuilder(self::NUMBERS, self::SEQUENTIAL_MIN_CHUNK);
        $score = 0;
        $txt = $text;
        foreach ($num as $value) {
            if (str_contains($txt, $value)) {
                $score += strlen($value) - (self::SEQUENTIAL_MIN_CHUNK - 1);
                $txt = str_replace($value, '', $txt);
            }
        }

        return $score * self::SEQUENTIAL_LETTERS_SCORE_RATIO;
    }

    private function getSequentialSymbolsScore(string $text): int
    {
        $score = 0;
        $sym = $this->getSymbols($text);
        if (!empty($sym)) {
            $seq = $this->sequentialBuilder($sym, self::SEQUENTIAL_MIN_CHUNK);
            $txt = $text;
            foreach ($seq as $value) {
                if (str_contains($txt, $value)) {
                    $score += strlen($value) - (self::SEQUENTIAL_MIN_CHUNK - 1);
                    $txt = str_replace($value, '', $txt);
                }
            }
            $score = $score * self::SEQUENTIAL_LETTERS_SCORE_RATIO;
        }
        return $score;
    }

    private function getRepeatCharactersScore(string $text): int
    {
        $score = 0;
        $matches = [];
        if (preg_match_all('/(.+)(?=.*?\1)/', $text, $matches) == 0) {
            return $score;
        }

        $maxResultLength = strlen($this->sortByLength($matches[0])[0]);

        $ratio = 0;
        if ($maxResultLength >= 1 and $maxResultLength <= 5) {
            $ratio = self::REPEAT_CHARACTERS_SCORE_RATIO_1_5;
        }
        if ($maxResultLength >= 6 and $maxResultLength <= 10) {
            $ratio = self::REPEAT_CHARACTERS_SCORE_RATIO_6_10;
        }
        if ($maxResultLength >= 11) {
            $ratio = self::REPEAT_CHARACTERS_SCORE_RATIO_11;
        }

        $score = $ratio * $maxResultLength + (strlen($text) - $maxResultLength * 2);

        return $score;
    }

    private function getRequirementsScore(string $text, bool $ignoreCase): array
    {
        $errors = [];

        $upperCount = preg_match_all('/[A-Z]/', $text);
        $lowerCount = preg_match_all('/[a-z]/', $text);
        $numbersCount = preg_match_all('/[0-9]/', $text);
        $symbolsCount = strlen($text) - ($upperCount + $lowerCount + $numbersCount);

        if ($this->getRequirements()->getMinLength()) {
            $msg = $this->getRequirements()->getMinLength();
            if (strlen($text) < $msg->getValue()) {
                $errors[] = $msg->getMessage();
            }
        }

        if ($this->getRequirements()->getMaxLength()) {
            $msg = $this->getRequirements()->getMaxLength();
            if (strlen($text) > $msg->getValue()) {
                $errors[] = $msg->getMessage();
            }
        }

        if ($this->getRequirements()->getStartsWith()) {
            $msg = $this->getRequirements()->getStartsWith();
            if (!str_starts_with($text, $msg->getValue())) {
                $errors[] = $msg->getMessage();
            }
        }

        if ($this->getRequirements()->getEndsWith()) {
            $msg = $this->getRequirements()->getEndsWith();
            if (!str_ends_with($text, $msg->getValue())) {
                $errors[] = $msg->getMessage();
            }
        }

        if ($this->getRequirements()->getUppercaseLettersMinLength()) {
            $msg = $this->getRequirements()->getUppercaseLettersMinLength();
            if ($msg->getValue() > $upperCount) {
                $errors[] = $msg->getMessage();
            }
        }

        if ($this->getRequirements()->getLowercaseLettersMinLength()) {
            $msg = $this->getRequirements()->getLowercaseLettersMinLength();
            if ($msg->getValue() > $lowerCount) {
                $errors[] = $msg->getMessage();
            }
        }

        if ($this->getRequirements()->getNumbersMinLength()) {
            $msg = $this->getRequirements()->getNumbersMinLength();
            if ($msg->getValue() > $numbersCount) {
                $errors[] = $msg->getMessage();
            }
        }

        if ($this->getRequirements()->getSymbolsMinLength()) {
            $msg = $this->getRequirements()->getSymbolsMinLength();
            if ($msg->getValue() > $symbolsCount) {
                $errors[] = $msg->getMessage();
            }
        }

        if ($this->getRequirements()->getUniqueLettersMinLength()) {
            $msg = $this->getRequirements()->getUniqueLettersMinLength();
            $isValid = count(array_unique(str_split($text))) >= $msg->getValue();
            if (!$isValid) {
                $errors[] = $msg->getMessage();
            }
        }

        if ($this->getRequirements()->getInclude()) {
            $msg = $this->getRequirements()->getInclude();
            if (!$this->contains($text, $msg->getValue(), $ignoreCase)) {
                $errors[] = $msg->getMessage();
            }
        }

        if ($this->getRequirements()->getExclude()) {
            $msg = $this->getRequirements()->getExclude();
            if (!$this->doesNotContains($text, $msg->getValue(), $ignoreCase)) {
                $errors[] = $msg->getMessage();
            }
        }

        if ($this->getRequirements()->getBlacklist()) {
            $msg = $this->getRequirements()->getBlacklist();
            if ($this->isInBlacklist($text, $msg->getValue(), $ignoreCase)) {
                $errors[] = $msg->getMessage();
            }
        }

        if ($this->getRequirements()->getIncludeOne()) {
            $msg = $this->getRequirements()->getIncludeOne();
            if (!$this->containsOne($text, $msg->getValue(), $ignoreCase)) {
                $errors[] = $msg->getMessage();
            }
        }

        return $errors;
    }

    public function getResult(string $password, bool $ignoreCase = false): Result
    {
        $result = new Result();

        $req = $this->getRequirementsScore($password, $ignoreCase);

        // Additions
        $len = $this->getLengthScore($password);
        $upper = $this->getUppercaseLettersScore($password);
        $lower = $this->getLowercaseLettersScore($password);
        $num = $this->getNumbersScore($password);
        $symbol = $this->getSymbolsScore($password);
        // Deductions
        $letterOnly = $this->getLettersOnlyScore($password);
        $numberOnly = $this->getNumbersOnlyScore($password);
        $repetition = $this->getRepeatCharactersScore($password);
        $consecutiveUpper = $this->getConsecutiveUppercaseLettersScore($password);
        $consecutiveLower = $this->getConsecutiveLowercaseLettersScore($password);
        $consecutiveNumber = $this->getConsecutiveNumbersScore($password);
        $seqLetters = $this->getSequentialLettersScore($password);
        $seqNumbers = $this->getSequentialNumbersScore($password);
        $seqSymbols = $this->getSequentialSymbolsScore($password);

        $result->setScore($len + $upper + $lower + $num + $symbol + $letterOnly + $numberOnly + $repetition + $consecutiveUpper + $consecutiveLower + $consecutiveNumber + $seqLetters + $seqNumbers + $seqSymbols);

        $range = $this->scoreRange->getScoreRange();
        $range = $this->replaceKey($range, '_', '9999');

        uksort($range, function ($a, $b) {
            if (is_null($a) or is_null($b)) {
                if ($a > $b) {
                    return 1;
                } else {
                    return -1;
                }
            }
            return intval($a) - intval($b);
        });

        $range = $this->replaceKey($range, '9999', '_');

        if (count($range) < 2) {
            throw new \ValueError("ScoreRangeProvider must provide at least 2 score ranges");
        }

        if (array_key_last($range) !== '_') {
            throw new \ValueError("The last key of the array ScoreRangeProvider provides must be '_' ");
        }

        $i = 0;
        $prevRangeKey = null;
        foreach ($range as $key => $value) {
            //first
            if ($i === 0) {
                if ($this->between($result->getScore(), 1, floatval($key))) {
                    $result->setStatus($value);
                    $prevRangeKey = $key;
                    break;
                }
            }
            //last
            if ($key === '_') {
                if ($this->between($result->getScore(), floatval($prevRangeKey), 1000000000000000000)) {
                    $result->setStatus($value);
                    $prevRangeKey = $key;
                    break;
                }
            }

            if ($this->between($result->getScore(), floatval($prevRangeKey), floatval($key))) {
                $result->setStatus($value);
            }
        }

        end($range);
        prev($range);
        $result->setPercent(($result->getScore() * 100) / floatval(key($range)));

        $result->setErrors($req);

        return $result;
    }

    /**
     * Returns and array of results
     */
    public function getResults(StringCollection $passwords, bool $ignoreCase): array
    {
        $results = [];
        foreach ($passwords->toArray() as $password) {
            $results[] = $this->getResult($password, $ignoreCase);
        }

        return $results;
    }
}