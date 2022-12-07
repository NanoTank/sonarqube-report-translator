<?php
declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue;

class SecondaryLocationsCollection implements \Iterator
{
    private array $issues = [];

    public function add(Location $issue): self
    {
        $this->issues[] = $issue;

        return $this;
    }

    public function remove(mixed $key): self
    {
        unset($this->issues[$key]);

        return $this;
    }

    public function current(): Location
    {
        return current($this->issues);
    }

    public function next(): void
    {
        next($this->issues);
    }

    public function key(): mixed
    {
        return key($this->issues);
    }

    public function valid(): bool
    {
        return null === key($this->issues);
    }

    public function rewind(): void
    {
        reset($this->issues);
    }
}
