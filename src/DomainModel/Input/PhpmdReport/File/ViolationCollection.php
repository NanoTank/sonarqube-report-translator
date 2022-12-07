<?php
declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Input\PhpmdReport\File;

use Powercloud\SRT\DomainModel\AbstractCollection;

class ViolationCollection extends AbstractCollection
{
    public function add(Violation $violation): self
    {
        $this->items[] = $violation;

        return $this;
    }

    public function current(): Violation|false
    {
        return current($this->items);
    }
}
