<?php

declare(strict_types=1);

namespace Powercloud\SRT\Command;

use Powercloud\SRT\DomainModel\Input\PhpcsReport;
use Powercloud\SRT\DomainModel\Transformer\PhpcsTransformer;
use Powercloud\SRT\DomainModel\Transformer\TransformerOptions;
use Powercloud\SRT\Exception\UnsupportedReportForTransformer;
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

    protected function configure(): void
    {
        parent::configure();

        $this->setName(name: 'srt:translate:phpcs');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws UnsupportedReportForTransformer
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $phpcsFileContent = $this->getFileContent((string) $input->getArgument('path')); // @phpstan-ignore-line

        /** @var PhpcsReport $phpcsReport */
        $phpcsReport = $this->serializer->deserialize($phpcsFileContent, PhpcsReport::class, 'json');

        $externalIssuesReport = $this->deptracTransformer->transform(
            $phpcsReport,
            new TransformerOptions($this->getSeverity($input), $this->getIssueType($input)),
        );

        $this->writeExternalIssueReportToFile(
            (string) $input->getArgument('externalIssuesReportPath'), // @phpstan-ignore-line
            $externalIssuesReport,
        );

        return 0;
    }
}
