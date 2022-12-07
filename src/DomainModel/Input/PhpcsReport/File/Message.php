<?php
declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Input\PhpcsReport\File;

/**
 * @codeCoverageIgnore
 */
class Message
{
    public function __construct(
        private readonly string $message,
        private readonly string $source,
        private readonly int $severity,
        private readonly bool $fixable,
        private readonly Message\TypeEnum $type,
        private readonly int $line,
        private readonly int $column,
    ) {}

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getSeverity(): int
    {
        return $this->severity;
    }

    public function isFixable(): bool
    {
        return $this->fixable;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getColumn(): int
    {
        return $this->column;
    }

}
