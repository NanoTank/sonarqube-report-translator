<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Input;

/**
 * @codeCoverageIgnore
 */
class DeptracReport
{
    public function __construct(
        private readonly DeptracReport\Report $report,
        /** @var DeptracReport\File[] $files */
        private readonly array $files,
    ) {
    }

    public function getReport(): DeptracReport\Report
    {
        return $this->report;
    }

    /**
     * @return DeptracReport\File[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }
}
