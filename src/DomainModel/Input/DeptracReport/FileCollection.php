<?php
declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Input\DeptracReport;

use Powercloud\SRT\DomainModel\AbstractCollection;

class FileCollection extends AbstractCollection
{
    public function add(File $file): self
    {
        $this->items[] = $file;

        return $this;
    }

    /**
     * @psalm-suppress MixedInferredReturnType,MixedReturnStatement
     */
    public function current(): File|false
    {
        return current($this->items);
    }
}
