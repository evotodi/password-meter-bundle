<?php

namespace Evotodi\PasswordMeterBundle\Models;

use Evotodi\PasswordMeterBundle\Interfaces\StringCollectionInterface;

final class StringCollection implements StringCollectionInterface
{
    /**
     * The items of this array.
     */
    protected array $data = [];

    /**
     * Constructs a new array object.
     */
    public function __construct(array $data = [])
    {
        // Invoke offsetSet() for each value added; in this way, sub-classes
        // may provide additional logic about values added to the array object.
        foreach ($data as $key => $value) {
            $this[$key] = $value;
        }
    }

    /**
     * @inheritdoc
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->data);
    }

    /**
     * @inheritdoc
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->data[$offset]);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->data[$offset];
    }

    /**
     * @inheritdoc
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!is_string($value) and !$value instanceof Message) {
            throw new \TypeError("Values must be of type string or a Message with the value as a string");
        }
        $this->data[] = $value;
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
    }

    /**
     * Returns data suitable for PHP serialization.
     */
    public function __serialize(): array
    {
        return $this->data;
    }

    /**
     * Adds unserialized data to the object.
     */
    public function __unserialize(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * @inheritdoc
     */
    public function clear(): void
    {
        $this->data = [];
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function isEmpty(): bool
    {
        return $this->data === [];
    }
}