<?php

namespace Powercloud\SRT\Service;

use Powercloud\SRT\DomainModel\Input\DeptracReport;
use Powercloud\SRT\DomainModel\Input\PhpcsReport;
use Powercloud\SRT\DomainModel\Input\PhpmdReport;
use Powercloud\SRT\DomainModel\Input\ReportInterface;
use Powercloud\SRT\Exception\UnsupportedReportException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ReportDeserializerService implements ReportDeserializerInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function deserialize(string $report): ReportInterface
    {
        $supportedFormats = [
            [
                'report' => DeptracReport::class,
                'format' => 'json',
            ],
            [
                'report' => PhpmdReport::class,
                'format' => 'json',
            ],
            [
                'report' => PhpcsReport::class,
                'format' => 'json',
            ],
        ];

        foreach ($supportedFormats as $supportedFormat) {
            $externalIssueReport = $this->attemptDeserialization(
                $report,
                $supportedFormat['report'],
                $supportedFormat['format'],
            );

            if (is_null($externalIssueReport)) {
                continue;
            }

            return $externalIssueReport;
        }

        throw new UnsupportedReportException(
            sprintf(
                'Failed to deserialize report, format not supported. Supported formats are: %s%s',
                PHP_EOL,
                json_encode($supportedFormats),
            )
        );
    }

    /**
     * @param string $report
     * @param class-string $targetReportClass
     *
     * @return ReportInterface|null
     */
    private function attemptDeserialization(string $report, string $targetReportClass, string $format): ?ReportInterface
    {
        try {
            $report = $this->serializer->deserialize($report, $targetReportClass, $format);

            if (!$report instanceof $targetReportClass) {
                return null;
            }

            return $report;
        } catch (\Throwable $exception) {
            $this->logger->info(
                sprintf(
                    'Attempted to deserialize report into a deptrac format, but failed with message %s',
                    $exception->getMessage(),
                ),
            );

            return null;
        }
    }
}