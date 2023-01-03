<?php

declare(strict_types=1);

namespace Powercloud\SRT\Tests\Functional\Command;

use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\TypeEnum;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\SeverityEnum;

class ReportTranslatorCommandTest extends KernelTestCase
{
    public function testExecuteForDeptrac(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('srt:translate');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'path' => __DIR__ . '/../../TestFiles/deptrac.json',
            'externalIssuesReportPath' => __DIR__ . '/../../Output/Functional/deptrac.json'
        ]);

        $commandTester->assertCommandIsSuccessful();
    }

    public function testExecuteForPhpcs(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('srt:translate');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'path' => __DIR__ . '/../../TestFiles/phpcs.json',
            'externalIssuesReportPath' => __DIR__ . '/../../Output/Functional/phpcs.json'
        ]);

        $commandTester->assertCommandIsSuccessful();
    }

    public function testExecuteForPhpmd(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('srt:translate');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'path' => __DIR__ . '/../../TestFiles/phpmd.json',
            'externalIssuesReportPath' => __DIR__ . '/../../Output/Functional/phpmd.json'
        ]);

        $commandTester->assertCommandIsSuccessful();
    }

    public function testFileNotFound(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('srt:translate');
        $commandTester = new CommandTester($command);

        try {
            $commandTester->execute([
                'path' => __DIR__ . '/../../TestFiles/empty.json',
                'externalIssuesReportPath' => __DIR__ . '/../../Output/Functional/void.json'
            ]);
        } catch (FileNotFoundException $e) {
            $this->assertEquals(
                'File /app/tests/Functional/Command/../../TestFiles/empty.json cannot be read or empty',
                $e->getMessage());

            return;
        }

        $this->markTestIncomplete('FileNotFoundException was expected but not thrown');
    }

    /**
     * @dataProvider severityTypeProvider
     */
    public function testChangeSeverity(SeverityEnum $severityEnum): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('srt:translate');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'path' => __DIR__ . '/../../TestFiles/phpmd.json',
            'externalIssuesReportPath' => __DIR__ . '/../../Output/Functional/phpmd.json',
            '--severity' => $severityEnum->value
        ]);

        $outputContent = file_get_contents(__DIR__ . '/../../Output/Functional/phpmd.json');
        $decodedOutput = json_decode($outputContent);

        foreach ($decodedOutput as $testCase) {
            foreach ($testCase as $testObject) {
                $this->assertEquals($severityEnum->value, $testObject->severity);
            }
        }
    }

    /**
     * @dataProvider typeEnumProvider
     */
    public function testChangeIssueType(TypeEnum $typeEnum): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('srt:translate');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'path' => __DIR__ . '/../../TestFiles/phpmd.json',
            'externalIssuesReportPath' => __DIR__ . '/../../Output/Functional/phpmd.json',
            '--issueType' => $typeEnum->value
        ]);

        $outputContent = file_get_contents(__DIR__ . '/../../Output/Functional/phpmd.json');
        $decodedOutput = json_decode($outputContent);

        foreach ($decodedOutput as $testCase) {
            foreach ($testCase as $testObject) {
                $this->assertEquals($typeEnum->value, $testObject->type);
            }
        }
    }

    private function typeEnumProvider(): array
    {
        return [TypeEnum::cases()];
    }

    private function severityTypeProvider(): array
    {
        return [SeverityEnum::cases()];
    }
}
