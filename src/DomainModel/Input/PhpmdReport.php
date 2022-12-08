<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Input;

/**
 * @codeCoverageIgnore
 */
class PhpmdReport
{
    public function __construct(
        private readonly string $version,
        private readonly string $package,
        private readonly string $timestamp,
        private readonly PhpmdReport\FileCollection $files,
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

    public function getFiles(): PhpmdReport\FileCollection
    {
        return $this->files;
    }
}
