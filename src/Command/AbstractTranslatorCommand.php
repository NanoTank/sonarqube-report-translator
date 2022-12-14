<?php

declare(strict_types=1);

namespace Powercloud\SRT\Command;

use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractTranslatorCommand extends Command
{
    public function __construct(
        protected SerializerInterface $serializer,
    ) {
        parent::__construct();
    }

    protected function configure()
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
                name: 'externalIssuesReportPath',
                mode: InputArgument::REQUIRED,
                description: 'The absolute path of the external issue report path where the sonarqube report is saved',
            )
            ->addArgument(
                name: 'severity',
                mode: InputArgument::OPTIONAL,
                description: sprintf(
                    'Forces all the issues in the report to be of the specified severity. Valid values are <comment>%s</comment>',
                    implode('</comment>, <comment>', $issueSeverities),
                ),
            )
            ->addArgument(
                name: 'issueType',
                mode: InputArgument::OPTIONAL,
                description: sprintf(
                    'Forces all the issues in the report to be the specified type. Valid values are <comment>%s</comment>',
                    implode('</comment>, <comment>', $issueTypes),
                ),
            );
    }

    /**
     * @param string $path
     *
     * @return string
     *
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
        file_put_contents($path, $this->serializer->serialize($report, 'json'));
    }

    protected function getSeverity(InputInterface $input): ?ExternalIssuesReport\GenericIssue\SeverityEnum
    {
        try {
            if (empty($input->getArgument('severity'))) {
                return null;
            }

            return ExternalIssuesReport\GenericIssue\SeverityEnum::tryFrom($input->getArgument('severity'));
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    protected function getIssueType(InputInterface $input): ?ExternalIssuesReport\GenericIssue\TypeEnum
    {
        try {
            if (empty($input->getArgument('issueType'))) {
                return null;
            }

            return ExternalIssuesReport\GenericIssue\TypeEnum::tryFrom($input->getArgument('issueType'));
        } catch (InvalidArgumentException) {
            return null;
        }
    }
}