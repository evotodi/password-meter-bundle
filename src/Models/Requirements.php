<?php

namespace Evotodi\PasswordMeterBundle\Models;

class Requirements
{
    protected ?Message $minLength = null;
    protected ?Message $maxLength = null;
    protected ?Message $uniqueLettersMinLength = null;
    protected ?Message $uppercaseLettersMinLength = null;
    protected ?Message $lowercaseLettersMinLength = null;
    protected ?Message $numbersMinLength = null;
    protected ?Message $symbolsMinLength = null;
    protected ?Message $include = null;
    protected ?Message $exclude = null;
    protected ?Message $blacklist = null;
    protected ?Message $includeOne = null;
    protected ?Message $startsWith = null;
    protected ?Message $endsWith = null;

    public function __construct(
        int|Message|null $minLength = null,
        int|Message|null $maxLength = null,
        int|Message|null $uniqueLettersMinLength = null,
        int|Message|null $uppercaseLettersMinLength = null,
        int|Message|null $lowercaseLettersMinLength = null,
        int|Message|null $numbersMinLength = null,
        int|Message|null $symbolsMinLength = null,
        StringCollection|Message|null $include = null,
        StringCollection|Message|null $exclude = null,
        StringCollection|Message|null $blacklist = null,
        StringCollection|Message|null $includeOne = null,
        string|Message|null $startsWith = null,
        string|Message|null $endsWith = null
    )
    {
        $this->setMinLength($minLength);
        $this->setMaxLength($maxLength);
        $this->setUniqueLettersMinLength($uniqueLettersMinLength);
        $this->setUppercaseLettersMinLength($uppercaseLettersMinLength);
        $this->setLowercaseLettersMinLength($lowercaseLettersMinLength);
        $this->setNumbersMinLength($numbersMinLength);
        $this->setSymbolsMinLength($symbolsMinLength);
        $this->setInclude($include);
        $this->setExclude($exclude);
        $this->setBlacklist($blacklist);
        $this->setStartsWith($startsWith);
        $this->setEndsWith($endsWith);
        $this->setIncludeOne($includeOne);
    }

    public function getMinLength(): ?Message
    {
        return $this->minLength;
    }

    public function setMinLength(int|Message|null $minLength): void
    {
        if ($minLength instanceof Message){
            $this->minLength = $minLength;
        }elseif (is_int($minLength)) {
            $this->minLength = new Message($minLength, sprintf('The minimum password length is %s.', $minLength));
        }

        if (!is_null($this->minLength) and $this->minLength->getValue() <= 0) {
            throw new \ValueError("minLength must be greater than 0");
        }
    }

    public function getMaxLength(): ?Message
    {
        return $this->maxLength;
    }

    public function setMaxLength(int|Message|null $maxLength): void
    {
        if ($maxLength instanceof Message){
            $this->maxLength = $maxLength;
        }elseif (is_int($maxLength)) {
            $this->maxLength = new Message($maxLength, sprintf('The maximum password length is %s.', $maxLength));
        }else{
            $this->maxLength = null;
        }

        if (!is_null($this->maxLength) and !is_null($this->minLength) and $this->maxLength->getValue() <= $this->minLength->getValue()) {
            throw new \ValueError("maxLength must be greater than minLength");
        }
    }

    public function getUniqueLettersMinLength(): ?Message
    {
        return $this->uniqueLettersMinLength;
    }

    public function setUniqueLettersMinLength(int|Message|null $uniqueLettersMinLength): void
    {
        if ($uniqueLettersMinLength instanceof Message){
            $this->uniqueLettersMinLength = $uniqueLettersMinLength;
        }elseif (is_int($uniqueLettersMinLength)) {
            $this->uniqueLettersMinLength = new Message($uniqueLettersMinLength, sprintf('You must use at least %s unique letter(s).', $uniqueLettersMinLength));
        }else{
            $this->uniqueLettersMinLength = null;
        }

        if (!is_null($this->uniqueLettersMinLength) and $this->uniqueLettersMinLength->getValue() <= 0) {
            throw new \ValueError("uniqueLettersMinLength must be greater 0");
        }
    }

    public function getUppercaseLettersMinLength(): ?Message
    {
        return $this->uppercaseLettersMinLength;
    }

    public function setUppercaseLettersMinLength(int|Message|null $uppercaseLettersMinLength): void
    {
        if ($uppercaseLettersMinLength instanceof Message){
            $this->uppercaseLettersMinLength = $uppercaseLettersMinLength;
        }elseif (is_int($uppercaseLettersMinLength)) {
            $this->uppercaseLettersMinLength = new Message($uppercaseLettersMinLength, sprintf('You must use at least %s uppercase letter(s).', $uppercaseLettersMinLength));
        }else{
            $this->uppercaseLettersMinLength = null;
        }

        if (!is_null($this->uppercaseLettersMinLength) and $this->uppercaseLettersMinLength->getValue() <= 0) {
            throw new \ValueError("uppercaseLettersMinLength must be greater 0");
        }
    }

    public function getLowercaseLettersMinLength(): ?Message
    {
        return $this->lowercaseLettersMinLength;
    }

    public function setLowercaseLettersMinLength(int|Message|null $lowercaseLettersMinLength): void
    {
        if ($lowercaseLettersMinLength instanceof Message){
            $this->lowercaseLettersMinLength = $lowercaseLettersMinLength;
        }elseif (is_int($lowercaseLettersMinLength)) {
            $this->lowercaseLettersMinLength = new Message($lowercaseLettersMinLength, sprintf('You must use at least %s lowercase letter(s).', $lowercaseLettersMinLength));
        }else{
            $this->lowercaseLettersMinLength = null;
        }

        if (!is_null($this->lowercaseLettersMinLength) and $this->lowercaseLettersMinLength->getValue() <= 0) {
            throw new \ValueError("lowercaseLettersMinLength must be greater 0");
        }
    }

    public function getNumbersMinLength(): ?Message
    {
        return $this->numbersMinLength;
    }

    public function setNumbersMinLength(int|Message|null $numbersMinLength): void
    {
        if ($numbersMinLength instanceof Message){
            $this->numbersMinLength = $numbersMinLength;
        }elseif (is_int($numbersMinLength)) {
            $this->numbersMinLength = new Message($numbersMinLength, sprintf('You must use at least %s number(s).', $numbersMinLength));
        }else{
            $this->numbersMinLength = null;
        }

        if (!is_null($this->numbersMinLength) and $this->numbersMinLength->getValue() <= 0) {
            throw new \ValueError("numbersMinLength must be greater 0");
        }
    }

    public function getSymbolsMinLength(): ?Message
    {
        return $this->symbolsMinLength;
    }

    public function setSymbolsMinLength(int|Message|null $symbolsMinLength): void
    {
        if ($symbolsMinLength instanceof Message){
            $this->symbolsMinLength = $symbolsMinLength;
        }elseif (is_int($symbolsMinLength)) {
            $this->symbolsMinLength = new Message($symbolsMinLength, sprintf('You must use at least %s symbol(s).', $symbolsMinLength));
        }else{
            $this->symbolsMinLength = null;
        }

        if (!is_null($this->symbolsMinLength) and $this->symbolsMinLength->getValue() <= 0) {
            throw new \ValueError("symbolsMinLength must be greater 0");
        }
    }

    public function getInclude(): ?Message
    {
        return $this->include;
    }

    public function setInclude(StringCollection|Message|null $include): void
    {
        if ($include instanceof Message){
            if (!$include->getValue() instanceof StringCollection) {
                throw new \TypeError("include Message::value must be of type StringCollection");
            }
            $this->include = $include;
        }elseif ($include instanceof StringCollection) {
            $this->include = new Message($include, sprintf('The Password must include [%s].', implode(', ', $include->toArray())));
        }else{
            $this->include = null;
        }
    }

    public function getExclude(): ?Message
    {
        return $this->exclude;
    }

    public function setExclude(StringCollection|Message|null $exclude): void
    {
        if ($exclude instanceof Message){
            if (!$exclude->getValue() instanceof StringCollection) {
                throw new \TypeError("exclude Message::value must be of type StringCollection");
            }
            $this->exclude = $exclude;
        }elseif ($exclude instanceof StringCollection) {
            $this->exclude = new Message($exclude, sprintf('The Password must exclude [%s].', implode(', ', $exclude->toArray())));
        }else{
            $this->exclude = null;
        }
    }

    public function getBlacklist(): ?Message
    {
        return $this->blacklist;
    }

    public function setBlacklist(StringCollection|Message|null $blacklist): void
    {
        if ($blacklist instanceof Message){
            if (!$blacklist->getValue() instanceof StringCollection) {
                throw new \TypeError("blacklist Message::value must be of type StringCollection");
            }
            $this->blacklist = $blacklist;
        }elseif ($blacklist instanceof StringCollection) {
            $this->blacklist = new Message($blacklist, 'Your password is in the blacklist.');
        }else{
            $this->blacklist = null;
        }
    }

    public function getIncludeOne(): ?Message
    {
        return $this->includeOne;
    }

    public function setIncludeOne(StringCollection|Message|null $includeOne): void
    {
        if ($includeOne instanceof Message){
            if (!$includeOne->getValue() instanceof StringCollection) {
                throw new \TypeError("includeOne Message::value must be of type StringCollection");
            }
            $this->includeOne = $includeOne;
        }elseif ($includeOne instanceof StringCollection) {
            $this->includeOne = new Message($includeOne, sprintf('The Password must include at least one item specified [%s] .', implode(', ', $includeOne->toArray())));
        }else{
            $this->includeOne = null;
        }
    }

    public function getStartsWith(): ?Message
    {
        return $this->startsWith;
    }

    public function setStartsWith(string|Message|null $startsWith): void
    {
        if ($startsWith instanceof Message){
            if (!is_string($startsWith->getValue())) {
                throw new \TypeError("startsWith Message::value must be of type string");
            }
            $this->startsWith = $startsWith;
        }elseif (is_string($startsWith)) {
            $this->startsWith = new Message($startsWith, sprintf('The password must start with "%s".', $startsWith));
        }else{
            $this->startsWith = null;
        }
    }

    public function getEndsWith(): ?Message
    {
        return $this->endsWith;
    }

    public function setEndsWith(string|Message|null $endsWith): void
    {
        if ($endsWith instanceof Message){
            if (!is_string($endsWith->getValue())) {
                throw new \TypeError("endsWith Message::value must be of type string");
            }
            $this->endsWith = $endsWith;
        }elseif (is_string($endsWith)) {
            $this->endsWith = new Message($endsWith, sprintf('The password must end with "%s".', $endsWith));
        }else{
            $this->endsWith = null;
        }
    }

}