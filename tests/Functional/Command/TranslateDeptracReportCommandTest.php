<?php

declare(strict_types=1);

namespace Powercloud\SRT\Tests\Functional\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TranslateDeptracReportCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('srt:translate:deptrac');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'path' => __DIR__ . '/../../TestFiles/deptrac.json',
            'externalIssuesReportPath' => '/../../Output/Functional/ExternalIssuesReport.json'
        ]);

        $commandTester->assertCommandIsSuccessful();
    }
}
