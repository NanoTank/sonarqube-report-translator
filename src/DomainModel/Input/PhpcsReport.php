<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Input;

/**
 * @codeCoverageIgnore
 */
class PhpcsReport
{
    public function __construct(
        private readonly PhpcsReport\Totals $totals,
        private readonly PhpcsReport\FileCollection $files,
    ) {
    }

    public function getTotals(): PhpcsReport\Totals
    {
        return $this->totals;
    }

    public function getFiles(): PhpcsReport\FileCollection
    {
        return $this->files;
    }
}
