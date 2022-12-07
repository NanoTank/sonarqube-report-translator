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
        private readonly File\ViolationCollection $violations,
    ) {}

    public function getFile(): string
    {
        return $this->file;
    }

    public function getViolations(): File\ViolationCollection
    {
        return $this->violations;
    }
}
