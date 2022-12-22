<?php

declare(strict_types=1);

namespace Powercloud\SRT\Service;

use Powercloud\SRT\DomainModel\Input\ReportInterface;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport;
use Powercloud\SRT\DomainModel\Transformer\TransformerOptions;
use Powercloud\SRT\Exception\UnsupportedReportException;

interface ReportToSonarqubeConverterInterface
{
    /**
     * @param ReportInterface $report
     * @param TransformerOptions $transformerOptions
     *
     * @return ExternalIssuesReport
     *
     * @throws UnsupportedReportException when the report could not be converted to an external issue report
     */
    public function convert(ReportInterface $report, TransformerOptions $transformerOptions): ExternalIssuesReport;
}
