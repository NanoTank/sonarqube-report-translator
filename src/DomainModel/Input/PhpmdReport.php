<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Input;

/**
 * @codeCoverageIgnore
 */
class PhpmdReport implements ReportInterface
{
    public function __construct(
        private readonly string $version,
        private readonly string $package,
        private readonly string $timestamp,
        /** @var PhpmdReport\File[] $files */
        private readonly array $files,
    ) {
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getPackage(): string
    {
        return $this->package;
    }

    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    /**
     * @return PhpmdReport\File[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }
}
