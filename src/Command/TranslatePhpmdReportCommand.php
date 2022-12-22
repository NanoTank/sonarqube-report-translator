<?php

declare(strict_types=1);

namespace Powercloud\SRT\Command;

use Powercloud\SRT\DomainModel\Input\PhpmdReport;
use Powercloud\SRT\DomainModel\Transformer\PhpmdTransformer;
use Powercloud\SRT\DomainModel\Transformer\TransformerOptions;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;

class TranslatePhpmdReportCommand extends AbstractTranslatorCommand
{
    public function __construct(
        private readonly PhpmdTransformer $deptracTransformer,
        SerializerInterface $serializer,
    ) {
        parent::__construct($serializer);
    }

    protected function configure()
    {
        $this
            ->setName(name: 'srt:translate:phpmd')
            ->addArgument(
                name: 'path',
                mode: InputArgument::REQUIRED,
                description: 'The absolute path to the phpmd report file in json format',
            );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $phpmdFileContent = $this->getFileContent($input->getArgument('path'));

        $phpmdReport = $this->serializer->deserialize($phpmdFileContent, PhpmdReport::class, 'json');

        $externalIssuesReport = $this->deptracTransformer->transform(
            $phpmdReport,
            new TransformerOptions($this->getSeverity($input), $this->getIssueType($input)),
        );

        $this->writeExternalIssueReportToFile(
            $input->getArgument('externalIssuesReportPath'),
            $externalIssuesReport,
        );

        return 0;
    }
}
