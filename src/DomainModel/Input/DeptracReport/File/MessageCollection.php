<?php
declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Input\DeptracReport\File;

use Powercloud\SRT\DomainModel\AbstractCollection;

class MessageCollection extends AbstractCollection
{
    public function add(Message $message): self
    {
        $this->items[] = $message;

        return $this;
    }

    /**
     * @psalm-suppress MixedInferredReturnType,MixedReturnStatement
     */
    public function current(): Message|false
    {
        return current($this->items);
    }
}
