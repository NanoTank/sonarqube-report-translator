<?php
declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\Location;

/**
 * @codeCoverageIgnore
 */
class TextRange
{
    public function __construct(
        private readonly int $startLine,
        private readonly int $endLine,
        private readonly int $startColumn,
        private readonly int $endColumn,
    ) {
    }

    public function getStartLine(): int
    {
        return $this->startLine;
    }

    public function getEndLine(): int
    {
        return $this->endLine;
    }

    public function getStartColumn(): int
    {
        return $this->startColumn;
    }

    public function getEndColumn(): int
    {
        return $this->endColumn;
    }
}
