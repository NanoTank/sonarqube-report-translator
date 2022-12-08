<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue;

use Powercloud\SRT\DomainModel\AbstractCollection;

/**
 * @template-extends AbstractCollection<Location>
 */
class SecondaryLocationCollection extends AbstractCollection
{
    public function add(Location $location): self
    {
        $this->items[] = $location;

        return $this;
    }

    public function current(): ?Location
    {
        return current($this->items) ?: null;
    }
}
