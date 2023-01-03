<?php

declare(strict_types=1);

namespace Powercloud\SRT\Tests\Functional\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TranslatePhpcsReportCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('srt:translate:phpcs');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'path' => __DIR__ . '/../../TestFiles/phpcs.json',
            'externalIssuesReportPath' => __DIR__ . '/../../Output/Functional/phpcs.json'
        ]);

        $commandTester->assertCommandIsSuccessful();
    }
}
