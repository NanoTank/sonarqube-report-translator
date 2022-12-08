<?php
declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue;

use Powercloud\SRT\DomainModel\AbstractCollection;

class SecondaryLocationCollection extends AbstractCollection
{
    public function add(Location $location): self
    {
        $this->items[] = $location;

        return $this;
    }

    /**
     * @psalm-suppress MixedInferredReturnType,MixedReturnStatement
     */
    public function current(): Location|false
    {
        return current($this->items);
    }
}
