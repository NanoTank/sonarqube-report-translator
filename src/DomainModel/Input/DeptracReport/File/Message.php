<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Input\DeptracReport\File;

use Powercloud\SRT\DomainModel\Input\DeptracReport\File\Message\TypeEnum;

/**
 * @codeCoverageIgnore
 */
class Message
{
    public function __construct(
        private readonly string $message,
        private readonly int $line,
        private readonly TypeEnum $type,
    ) {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getType(): TypeEnum
    {
        return $this->type;
    }
}
