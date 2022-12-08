<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Input\DeptracReport;

use Powercloud\SRT\DomainModel\AbstractCollection;

/**
 * @template-extends AbstractCollection<File>
 */
class FileCollection extends AbstractCollection
{
    public function add(File $file): self
    {
        $this->items[] = $file;

        return $this;
    }

    public function current(): ?File
    {
        return current($this->items) ?: null;
    }
}
