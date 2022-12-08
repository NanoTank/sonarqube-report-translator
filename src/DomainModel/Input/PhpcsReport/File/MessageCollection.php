<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Input\PhpcsReport\File;

use Powercloud\SRT\DomainModel\AbstractCollection;

/**
 * @template-extends AbstractCollection<Message>
 */
class MessageCollection extends AbstractCollection
{
    public function add(Message $message): self
    {
        $this->items[] = $message;

        return $this;
    }

    public function current(): ?Message
    {
        return current($this->items) ?: null;
    }
}
