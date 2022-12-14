<?php

namespace Powercloud\SRT\Service;

use Powercloud\SRT\DomainModel\Input\ReportInterface;
use Powercloud\SRT\Exception\UnsupportedReportException;

interface ReportDeserializerInterface
{
    /**
     * @param string $report
     *
     * @return ReportInterface
     *
     * @throws UnsupportedReportException when the report cannot be deserialized
     */
    public function deserialize(string $report): ReportInterface;
}