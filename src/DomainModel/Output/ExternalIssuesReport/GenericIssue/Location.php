<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue;

use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\Location\TextRange;

/**
 * @codeCoverageIgnore
 */
class Location
{
    public function __construct(
        private readonly string $message,
        private readonly string $filePath,
        private readonly TextRange $textRange,
    ) {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getTextRange(): TextRange
    {
        return $this->textRange;
    }
}
