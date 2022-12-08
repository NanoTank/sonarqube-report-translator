<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Input\DeptracReport;

use Powercloud\SRT\DomainModel\Input\DeptracReport\File\MessageCollection;

/**
 * @codeCoverageIgnore
 */
class File
{
    public function __construct(
        private readonly int $violations,
        private readonly MessageCollection $messages,
    ) {
    }

    public function getViolations(): int
    {
        return $this->violations;
    }

    public function getMessages(): MessageCollection
    {
        return $this->messages;
    }
}
