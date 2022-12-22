<?php

declare(strict_types=1);

namespace Powercloud\SRT\Command;

use Powercloud\SRT\DomainModel\Transformer\TransformerOptions;
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

    protected function configure()
    {
        $this
            ->setName(name: 'srt:translate')
            ->addArgument(
                name: 'path',
                mode: InputArgument::REQUIRED,
                description: 'The absolute path to the report file format',
            );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $reportContent = $this->getFileContent($input->getArgument('path'));

        $report = $this->deserializer->deserialize($reportContent);

        $externalIssuesReport = $this->reportToSonarqubeConverter->convert(
            $report,
            new TransformerOptions($this->getSeverity($input), $this->getIssueType($input)),
        );

        $this->writeExternalIssueReportToFile(
            $input->getArgument('externalIssuesReportPath'),
            $externalIssuesReport,
        );

        return 0;
    }
}
