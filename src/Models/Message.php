<?php

namespace Evotodi\PasswordMeterBundle\Models;

class Message
{
    public const STR_MESSAGE = 1;
    public const INT_MESSAGE = 2;
    public const COL_MESSAGE = 2;

    private string|int|StringCollection $value;
    private string $message;

    public function __construct(int|string|StringCollection $value, string $message)
    {
        $this->value = $value;
        $this->message = $message;
    }

    public function getType(): int
    {
        if (is_string($this->value)){
            return self::STR_MESSAGE;
        }elseif (is_int($this->value)) {
            return self::INT_MESSAGE;
        }elseif ($this->value instanceof StringCollection) {
            return self::COL_MESSAGE;
        }else{
            throw new \TypeError("Invalid message type! Value must be a string, int, or StringCollection.");
        }
    }

    public function getValue(): string|int|StringCollection
    {
        return $this->value;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}