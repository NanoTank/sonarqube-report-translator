<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Output\ExternalIssuesReport;

use Powercloud\SRT\DomainModel\AbstractCollection;

/**
 * @template-extends AbstractCollection<GenericIssue>
 */
class GenericIssueCollection extends AbstractCollection
{
    public function add(GenericIssue $issue): self
    {
        $this->items[] = $issue;

        return $this;
    }

    public function current(): ?GenericIssue
    {
        return current($this->items) ?: null;
    }
}
