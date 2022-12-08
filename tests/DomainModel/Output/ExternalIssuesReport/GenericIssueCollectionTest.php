<?php

declare(strict_types=1);

namespace Powercloud\SRT\Tests\DomainModel\Output\ExternalIssuesReport;

use PHPUnit\Framework\TestCase;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssueCollection;

class GenericIssueCollectionTest extends TestCase
{
    private GenericIssue $genericIssue1;

    private GenericIssue $genericIssue2;

    private GenericIssueCollection $testObject;

    public function setUp(): void
    {
        $this->genericIssue1 = new GenericIssue(
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

        $this->genericIssue2 = new GenericIssue(
            engineId: 'testEngine',
            ruleId: 'testRule',
            severity: GenericIssue\SeverityEnum::Critical,
            type: GenericIssue\TypeEnum::Vulnerability,
            primaryLocation: new GenericIssue\Location(
                message: 'test',
                filePath: '/src',
                textRange: new GenericIssue\Location\TextRange(
                    startLine: 5,
                    endLine: 6,
                    startColumn: 7,
                    endColumn: 8,
                )
            )
        );

        $this->testObject = new GenericIssueCollection();
    }

    public function testAddWithWrongTypeThrowsErrorException(): void
    {
        $this->expectError();
        $this->expectErrorMessage(
            sprintf(
                'Argument #1 ($issue) must be of type %s, class@anonymous given',
                GenericIssue::class,
            ),
        );

        $this->testObject->add(
            new class
            {
            },
        );
    }

    public function testAddMultiple(): void
    {
        $this->testObject->add($this->genericIssue1);
        $this->testObject->add($this->genericIssue2);

        $this->testObject->rewind();
        $this->assertSame($this->genericIssue1, $this->testObject->current());
        $this->testObject->next();
        $this->assertSame($this->genericIssue2, $this->testObject->current());
    }

    public function testCurrentAndNext()
    {
        $this->testObject->add($this->genericIssue1);
        $this->testObject->add($this->genericIssue2);
        $this->testObject->rewind();

        $this->assertSame($this->genericIssue1, $this->testObject->current());

        $this->testObject->next();

        $this->assertSame($this->genericIssue2, $this->testObject->current());
    }
}
