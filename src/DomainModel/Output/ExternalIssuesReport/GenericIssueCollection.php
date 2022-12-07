<?php
declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Output\ExternalIssuesReport;

class GenericIssueCollection implements \Iterator
{
    private array $issues = [];

    public function add(GenericIssue $issue): self
    {
        $this->issues[] = $issue;

        return $this;
    }

    public function remove(mixed $key): self
    {
        unset($this->issues[$key]);

        return $this;
    }

    public function current(): GenericIssue|false
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
        return false !== $this->current();
    }

    public function rewind(): void
    {
        reset($this->issues);
    }
}
