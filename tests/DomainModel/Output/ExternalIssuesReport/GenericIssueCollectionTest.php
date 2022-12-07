<?php
declare(strict_types=1);

namespace Powercloud\SRT\Tests\DomainModel\Output\ExternalIssuesReport;

use PHPUnit\Framework\TestCase;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssueCollection;

class GenericIssueCollectionTest extends TestCase
{
    private GenericIssueCollection $testObject;

    public function setUp(): void
    {
        $this->testObject = new GenericIssueCollection();
    }

    public function testAddWithWrongTypeThrowsErrorException(): void
    {
        $this->expectError();
        $this->expectErrorMessage(
            sprintf(
                'Argument #1 ($issue) must be of type %s, class@anonymous given',
                GenericIssue::class
            )
        );

        $this->testObject->add(new class{});
    }

    public function testAddMultiple(): void
    {
        $genericIssue1 = new GenericIssue(
            engineId: 'testEngine',
            ruleId: 'testRule',
            severity: GenericIssue\SeverityEnum::Blocker,
            type: GenericIssue\TypeEnum::Bug,
            primaryLocation: new GenericIssue\Location(
                message: 'test',
                filePath: '/src',
                textRange: new GenericIssue\Location\TextRange(
                    startLine: 1,
                    endLine: 2,
                    startColumn: 3,
                    endColumn: 4,
                )
            )
        );

        $genericIssue2 = new GenericIssue(
            engineId: 'testEngine',
            ruleId: 'testRule',
            severity: GenericIssue\SeverityEnum::Critical,
            type: GenericIssue\TypeEnum::Vulnerability,
            primaryLocation: new GenericIssue\Location(
                message: 'test',
                filePath: '/src',
                textRange: new GenericIssue\Location\TextRange(
                    startLine: 1,
                    endLine: 2,
                    startColumn: 3,
                    endColumn: 4,
                )
            )
        );

        $this->testObject->add($genericIssue1);
        $this->testObject->add($genericIssue2);

        $this->testObject->rewind();
        $this->assertSame($genericIssue1, $this->testObject->current());
        $this->testObject->next();
        $this->assertSame($genericIssue2, $this->testObject->current());
    }

    public function testCurrentAndNext()
    {
        $genericIssue1 = new GenericIssue(
            engineId: 'testEngine',
            ruleId: 'testRule',
            severity: GenericIssue\SeverityEnum::Blocker,
            type: GenericIssue\TypeEnum::Bug,
            primaryLocation: new GenericIssue\Location(
                message: 'test',
                filePath: '/src',
                textRange: new GenericIssue\Location\TextRange(
                    startLine: 1,
                    endLine: 2,
                    startColumn: 3,
                    endColumn: 4,
                )
            )
        );

        $genericIssue2 = new GenericIssue(
            engineId: 'testEngine',
            ruleId: 'testRule',
            severity: GenericIssue\SeverityEnum::Critical,
            type: GenericIssue\TypeEnum::Vulnerability,
            primaryLocation: new GenericIssue\Location(
                message: 'test',
                filePath: '/src',
                textRange: new GenericIssue\Location\TextRange(
                    startLine: 1,
                    endLine: 2,
                    startColumn: 3,
                    endColumn: 4,
                )
            )
        );

        $this->testObject->add($genericIssue1);
        $this->testObject->add($genericIssue2);
        $this->testObject->rewind();

        $this->assertSame($genericIssue1, $this->testObject->current());

        $this->testObject->next();

        $this->assertSame($genericIssue2, $this->testObject->current());
    }
}
