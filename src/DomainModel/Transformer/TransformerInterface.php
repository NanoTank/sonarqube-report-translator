<?php

declare(strict_types=1);

namespace Powercloud\SRT\DomainModel\Transformer;

use Powercloud\SRT\DomainModel\Input\ReportInterface;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport;
use Powercloud\SRT\Exception\UnsupportedReportForTransformer;

interface TransformerInterface
{
    /**
     * @param ReportInterface $report
     * @param TransformerOptions $transformerOptions
     *
     * @return ExternalIssuesReport
     *
     * @throws UnsupportedReportForTransformer when the report is not supported by a specific transformer
     */
    public function transform(
        ReportInterface $report,
        TransformerOptions $transformerOptions = new TransformerOptions(),
    ): ExternalIssuesReport;

    public function supports(ReportInterface $report): bool;
}
