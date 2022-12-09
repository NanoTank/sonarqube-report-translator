<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Input;

/**
 * @codeCoverageIgnore
 */
class PhpcsReport implements ReportInterface
{
    public function __construct(
        private readonly PhpcsReport\Totals $totals,
        /** @var PhpcsReport\File[] $files */
        private readonly array $files,
    ) {
    }

    public function getTotals(): PhpcsReport\Totals
    {
        return $this->totals;
    }

    /**
     * @return PhpcsReport\File[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }
}
