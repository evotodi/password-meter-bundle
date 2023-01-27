<?php

namespace Evotodi\PasswordMeterBundle\Interfaces;

interface StringCollectionInterface extends \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * Removes all items from this array.
     */
    public function clear(): void;

    /**
     * Returns a native PHP array representation of this array object.
     */
    public function toArray(): array;

    /**
     * Returns `true` if this array is empty.
     */
    public function isEmpty(): bool;
}