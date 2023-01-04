<?php

declare(strict_types=1);

namespace Powercloud\SRT\Command;

use Powercloud\SRT\DomainModel\Input\DeptracReport;
use Powercloud\SRT\DomainModel\Transformer\DeptracTransformer;
use Powercloud\SRT\DomainModel\Transformer\TransformerOptions;
use Powercloud\SRT\Exception\UnsupportedReportForTransformer;
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

    protected function configure(): void
    {
        parent::configure();

        $this->setName(name: 'srt:translate:deptrac');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws UnsupportedReportForTransformer
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $deptracFileContent = $this->getFileContent((string) $input->getArgument('path')); // @phpstan-ignore-line

        /** @var DeptracReport $deptracReport */
        $deptracReport = $this->serializer->deserialize($deptracFileContent, DeptracReport::class, 'json');

        $externalIssuesReport = $this->deptracTransformer->transform(
            $deptracReport,
            new TransformerOptions($this->getSeverity($input), $this->getIssueType($input)),
        );

        $this->writeExternalIssueReportToFile(
            (string) $input->getArgument('externalIssuesReportPath'), // @phpstan-ignore-line
            $externalIssuesReport,
        );

        return 0;
    }
}
