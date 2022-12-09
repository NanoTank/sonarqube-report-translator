<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Input\PhpcsReport;

/**
 * @codeCoverageIgnore
 */
class File
{
    public function __construct(
        private readonly string $name,
        private readonly int $errors,
        private readonly int $warnings,
        /** @var File\Message[] $messages */
        private readonly array $messages,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getErrors(): int
    {
        return $this->errors;
    }

    public function getWarnings(): int
    {
        return $this->warnings;
    }

    /**
     * @return File\Message[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}
