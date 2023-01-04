<?php

declare(strict_types=1);

namespace Powercloud\SRT\Command;

use Powercloud\SRT\DomainModel\Transformer\TransformerOptions;
use Powercloud\SRT\Exception\UnsupportedReportException;
use Powercloud\SRT\Service\ReportDeserializerInterface;
use Powercloud\SRT\Service\ReportToSonarqubeConverterInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ReportTranslatorCommand extends AbstractTranslatorCommand
{
    public function __construct(
        private readonly ReportToSonarqubeConverterInterface $reportToSonarqubeConverter,
        private readonly ReportDeserializerInterface $deserializer,
        SerializerInterface $serializer,
    ) {
        parent::__construct($serializer);
    }

    protected function configure(): void
    {
        parent::configure();

        $this->setName(name: 'srt:translate');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws UnsupportedReportException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $reportContent = $this->getFileContent((string) $input->getArgument('path'));

        $report = $this->deserializer->deserialize($reportContent);

        $externalIssuesReport = $this->reportToSonarqubeConverter->convert(
            $report,
            new TransformerOptions($this->getSeverity($input), $this->getIssueType($input)),
        );

        $this->writeExternalIssueReportToFile(
            (string) $input->getArgument('externalIssuesReportPath'),
            $externalIssuesReport,
        );

        return 0;
    }
}
