<?php

declare(strict_types=1);

namespace Powercloud\SRT\Service;

use Powercloud\SRT\DomainModel\Input\ReportInterface;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport;
use Powercloud\SRT\DomainModel\Transformer\TransformerInterface;
use Powercloud\SRT\DomainModel\Transformer\TransformerOptions;
use Powercloud\SRT\Exception\InvalidParameterException;
use Powercloud\SRT\Exception\UnsupportedReportException;
use Powercloud\SRT\Exception\UnsupportedReportForTransformer;

class ReportToSonarqubeConverterService implements ReportToSonarqubeConverterInterface
{
    /**
     * @var TransformerInterface[] $transformers
     */
    private readonly iterable $transformers;

    /**
     * @param TransformerInterface[] $transformers
     * @throws InvalidParameterException
     */
    public function __construct(
        iterable $transformers,
    ) {
        foreach ($transformers as $transformer) {
            if (!$transformer instanceof TransformerInterface) {
                throw new InvalidParameterException(
                    message: sprintf(
                        'Parameter of type [%s] expected, but [%s] received',
                        TransformerInterface::class,
                        get_debug_type($transformer),
                    ),
                    code: 1,
                    severity: \E_ERROR,
                );
            }
        }

        $this->transformers = $transformers;
    }

    public function convert(ReportInterface $report, TransformerOptions $transformerOptions): ExternalIssuesReport
    {
        foreach ($this->transformers as $transformer) {
            if (!$transformer->supports($report)) {
                continue;
            }
            $externalIssuesReport = $this->attemptTransformation($transformer, $report, $transformerOptions);

            if (is_null($externalIssuesReport)) {
                continue;
            }

            return $externalIssuesReport;
        }

        throw new UnsupportedReportException();
    }

    private function attemptTransformation(
        TransformerInterface $transformer,
        ReportInterface $report,
        TransformerOptions $transformerOptions,
    ): ?ExternalIssuesReport {
        try {
            return $transformer->transform($report, $transformerOptions);
        } catch (UnsupportedReportForTransformer) {
            return null;
        }
    }
}
