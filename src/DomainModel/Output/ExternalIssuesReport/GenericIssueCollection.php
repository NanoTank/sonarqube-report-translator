<?php
declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Output\ExternalIssuesReport;

use Powercloud\SRT\DomainModel\AbstractCollection;

class GenericIssueCollection extends AbstractCollection
{
    public function add(GenericIssue $issue): self
    {
        $this->items[] = $issue;

        return $this;
    }

    public function current(): GenericIssue|false
    {
        return current($this->items);
    }
}
