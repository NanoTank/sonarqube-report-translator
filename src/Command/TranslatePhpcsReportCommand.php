<?php

declare(strict_types=1);

namespace Powercloud\SRT\Command;

use Powercloud\SRT\DomainModel\Input\PhpcsReport;
use Powercloud\SRT\DomainModel\Transformer\PhpcsTransformer;
use Powercloud\SRT\DomainModel\Transformer\TransformerOptions;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;

class TranslatePhpcsReportCommand extends AbstractTranslatorCommand
{
    public function __construct(
        private readonly PhpcsTransformer $deptracTransformer,
        SerializerInterface $serializer,
    ) {
        parent::__construct($serializer);
    }

    protected function configure()
    {
        parent::configure();

        $this->setName(name: 'srt:translate:phpcs');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $phpcsFileContent = $this->getFileContent($input->getArgument('path'));

        $phpcsReport = $this->serializer->deserialize($phpcsFileContent, PhpcsReport::class, 'json');

        $externalIssuesReport = $this->deptracTransformer->transform(
            $phpcsReport,
            new TransformerOptions($this->getSeverity($input), $this->getIssueType($input)),
        );

        $this->writeExternalIssueReportToFile(
            $input->getArgument('externalIssuesReportPath'),
            $externalIssuesReport,
        );

        return 0;
    }
}
