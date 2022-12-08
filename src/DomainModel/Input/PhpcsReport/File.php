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
        private readonly File\MessageCollection $messages,
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

    public function getMessages(): File\MessageCollection
    {
        return $this->messages;
    }
}
