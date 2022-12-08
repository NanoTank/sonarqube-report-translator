<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel;

/**
 * @template TValue
 * @template-implements \Iterator<int, TValue>
 */
abstract class AbstractCollection implements \Iterator
{
    /**
     * @var array<int, TValue>
     */
    protected array $items = [];

    /**
     * @return AbstractCollection<TValue>
     */
    public function remove(int $key): self
    {
        unset($this->items[$key]);

        return $this;
    }

    public function next(): void
    {
        next($this->items);
    }

    public function key(): int|null
    {
        return key($this->items);
    }

    public function valid(): bool
    {
        return null !== $this->current();
    }

    public function rewind(): void
    {
        reset($this->items);
    }
}
