<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Input\PhpmdReport;

/**
 * @codeCoverageIgnore
 */
class File
{
    public function __construct(
        private readonly string $file,
        /** @var File\Violation[] $violations */
        private readonly array $violations,
    ) {
    }

    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @return File\Violation[]
     */
    public function getViolations(): array
    {
        return $this->violations;
    }
}
