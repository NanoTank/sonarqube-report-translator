<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Input\DeptracReport;

/**
 * @codeCoverageIgnore
 */
class File
{
    public function __construct(
        private readonly int $violations,
        /** @var File\Message[] $messages */
        private readonly array $messages,
    ) {
    }

    public function getViolations(): int
    {
        return $this->violations;
    }

    /**
     * @return File\Message[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}
