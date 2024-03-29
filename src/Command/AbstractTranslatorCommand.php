<?php

declare(strict_types=1);

namespace Powercloud\SRT\Command;

use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractTranslatorCommand extends Command
{
    public function __construct(
        protected SerializerInterface $serializer,
    ) {
        parent::__construct();
    }

    /** @SuppressWarnings(PHPMD.StaticAccess) */
    protected function configure(): void
    {
        parent::configure();
        $issueTypes = [];
        foreach (ExternalIssuesReport\GenericIssue\TypeEnum::cases() as $type) {
            $issueTypes[] = $type->value;
        }
        $issueSeverities = [];
        foreach (ExternalIssuesReport\GenericIssue\SeverityEnum::cases() as $severity) {
            $issueSeverities[] = $severity->value;
        }

        $this
            ->addArgument(
                name: 'path',
                mode: InputArgument::REQUIRED,
                description: 'The absolute path to the phpmd report file in json format',
            )
            ->addArgument(
                name: 'externalIssuesReportPath',
                mode: InputArgument::REQUIRED,
                description: 'The absolute path of the external issue report path where the sonarqube report is saved',
            )
            ->addOption(
                name: 'severity',
                shortcut: 's',
                mode: InputOption::VALUE_OPTIONAL,
                description: sprintf(
                    'Forces all the issues in the report to be of the specified severity. '
                    . 'Valid values are <comment>%s</comment>',
                    implode('</comment>, <comment>', $issueSeverities),
                ),
            )
            ->addOption(
                name: 'issueType',
                shortcut: 'i',
                mode: InputOption::VALUE_OPTIONAL,
                description: sprintf(
                    'Forces all the issues in the report to be the specified type. '
                    . 'Valid values are <comment>%s</comment>',
                    implode('</comment>, <comment>', $issueTypes),
                ),
            );
    }

    /**
     * @throws FileNotFoundException when the file cannot be read or it is empty
     */
    protected function getFileContent(string $path): string
    {
        $content = file_get_contents($path);

        if (empty($content)) {
            throw new FileNotFoundException(sprintf('File %s cannot be read or empty', $path));
        }

        return $content;
    }

    protected function writeExternalIssueReportToFile(string $path, ExternalIssuesReport $report): void
    {
        file_put_contents(
            $path,
            $this->serializer->serialize(
                $report,
                'json',
                [
                    AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
                ],
            ),
        );
    }

    /** @SuppressWarnings(PHPMD.StaticAccess) */
    protected function getSeverity(InputInterface $input): ?ExternalIssuesReport\GenericIssue\SeverityEnum
    {
        // @phpstan-ignore-next-line
        return ExternalIssuesReport\GenericIssue\SeverityEnum::tryFrom((string)$input->getOption('severity'));
    }

    /** @SuppressWarnings(PHPMD.StaticAccess) */
    protected function getIssueType(InputInterface $input): ?ExternalIssuesReport\GenericIssue\TypeEnum
    {
        // @phpstan-ignore-next-line
        return ExternalIssuesReport\GenericIssue\TypeEnum::tryFrom((string)$input->getOption('issueType'));
    }
}
