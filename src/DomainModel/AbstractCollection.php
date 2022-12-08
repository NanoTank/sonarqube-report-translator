<?php
declare(strict_types=1);

namespace Powercloud\SRT\DomainModel;

abstract class AbstractCollection implements \Iterator
{
    protected array $items = [];

    public function remove(string|int $key): self
    {
        unset($this->items[$key]);

        return $this;
    }

    public function next(): void
    {
        next($this->items);
    }

    public function key(): string|int
    {
        return key($this->items);
    }

    public function valid(): bool
    {
        return false !== $this->current();
    }

    public function rewind(): void
    {
        reset($this->items);
    }
}
