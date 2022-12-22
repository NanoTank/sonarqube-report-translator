<?php

declare(strict_types=1);

namespace Powercloud\SRT\Command;

use Powercloud\SRT\DomainModel\Input\DeptracReport;
use Powercloud\SRT\DomainModel\Transformer\DeptracTransformer;
use Powercloud\SRT\DomainModel\Transformer\TransformerOptions;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;

class TranslateDeptracReportCommand extends AbstractTranslatorCommand
{
    public function __construct(
        private readonly DeptracTransformer $deptracTransformer,
        SerializerInterface $serializer,
    ) {
        parent::__construct($serializer);
    }

    protected function configure()
    {
        $this
            ->setName(name: 'srt:translate:deptrac')
            ->addArgument(
                name: 'path',
                mode: InputArgument::REQUIRED,
                description: 'The absolute path to the deptrac report file in json format',
            );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $deptracFileContent = $this->getFileContent($input->getArgument('path'));

        $deptracReport = $this->serializer->deserialize($deptracFileContent, DeptracReport::class, 'json');

        $externalIssuesReport = $this->deptracTransformer->transform(
            $deptracReport,
            new TransformerOptions($this->getSeverity($input), $this->getIssueType($input)),
        );

        $this->writeExternalIssueReportToFile(
            $input->getArgument('externalIssuesReportPath'),
            $externalIssuesReport,
        );

        return 0;
    }
}
