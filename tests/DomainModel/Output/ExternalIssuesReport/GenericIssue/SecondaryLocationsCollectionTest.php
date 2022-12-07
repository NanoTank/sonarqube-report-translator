<?php
declare(strict_types=1);

namespace Powercloud\SRT\Tests\DomainModel\Output\ExternalIssuesReport\GenericIssue;

use PHPUnit\Framework\TestCase;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\Location;
use Powercloud\SRT\DomainModel\Output\ExternalIssuesReport\GenericIssue\SecondaryLocationsCollection;

class SecondaryLocationsCollectionTest extends TestCase
{
    private SecondaryLocationsCollection $testObject;

    public function setUp(): void
    {
        $this->testObject = new SecondaryLocationsCollection();
    }

    public function testAddWithWrongTypeThrowsErrorException(): void
    {
        $this->expectError();
        $this->expectErrorMessage(
            sprintf(
                'Argument #1 ($issue) must be of type %s, class@anonymous given',
                Location::class
            )
        );

        $this->testObject->add(new class{});
    }

    public function testAddMultiple(): void
    {
        $location1 = new Location(
            message: 'test location1',
            filePath: '/src/file1',
            textRange: new Location\TextRange(
                startLine: 1,
                endLine: 2,
                startColumn: 3,
                endColumn: 4,
            ),
        );

        $location2 = new Location(
            message: 'test location2',
            filePath: '/src/file2',
            textRange: new Location\TextRange(
                startLine: 5,
                endLine: 6,
                startColumn: 7,
                endColumn: 8,
            ),
        );

        $this->testObject->add($location1);
        $this->testObject->add($location2);

        $this->testObject->rewind();
        $this->assertSame($location1, $this->testObject->current());
        $this->testObject->next();
        $this->assertSame($location2, $this->testObject->current());
    }

    /**
     * @depends testAddMultiple
     */
    public function testRemove()
    {
        $location1 = new Location(
            message: 'test location1',
            filePath: '/src/file1',
            textRange: new Location\TextRange(
                startLine: 1,
                endLine: 2,
                startColumn: 3,
                endColumn: 4,
            ),
        );

        $location2 = new Location(
            message: 'test location2',
            filePath: '/src/file2',
            textRange: new Location\TextRange(
                startLine: 5,
                endLine: 6,
                startColumn: 7,
                endColumn: 8,
            ),
        );

        $this->testObject->add($location1);
        $this->testObject->add($location2);

        $this->testObject->rewind();
        $this->testObject->remove($this->testObject->key());

        $this->testObject->rewind();
        $this->assertSame($location2, $this->testObject->current());

        $this->testObject->next();

        $this->assertFalse($this->testObject->valid());
    }

    public function testCurrentAndNext()
    {
        $location1 = new Location(
            message: 'test location1',
            filePath: '/src/file1',
            textRange: new Location\TextRange(
                startLine: 1,
                endLine: 2,
                startColumn: 3,
                endColumn: 4,
            ),
        );

        $location2 = new Location(
            message: 'test location2',
            filePath: '/src/file2',
            textRange: new Location\TextRange(
                startLine: 5,
                endLine: 6,
                startColumn: 7,
                endColumn: 8,
            ),
        );

        $this->testObject->add($location1);
        $this->testObject->add($location2);
        $this->testObject->rewind();

        $this->assertSame($location1, $this->testObject->current());

        $this->testObject->next();

        $this->assertSame($location2, $this->testObject->current());
    }

    public function testKey()
    {
        $location1 = new Location(
            message: 'test location1',
            filePath: '/src/file1',
            textRange: new Location\TextRange(
                startLine: 1,
                endLine: 2,
                startColumn: 3,
                endColumn: 4,
            ),
        );

        $location2 = new Location(
            message: 'test location2',
            filePath: '/src/file2',
            textRange: new Location\TextRange(
                startLine: 5,
                endLine: 6,
                startColumn: 7,
                endColumn: 8,
            ),
        );

        $this->testObject->add($location1);
        $this->testObject->add($location2);

        $this->testObject->rewind();

        $this->assertEquals(0, $this->testObject->key());
        $this->testObject->next();
        $this->assertEquals(1, $this->testObject->key());
    }

    public function testValid()
    {
        $location1 = new Location(
            message: 'test location1',
            filePath: '/src/file1',
            textRange: new Location\TextRange(
                startLine: 1,
                endLine: 2,
                startColumn: 3,
                endColumn: 4,
            ),
        );

        $location2 = new Location(
            message: 'test location2',
            filePath: '/src/file2',
            textRange: new Location\TextRange(
                startLine: 5,
                endLine: 6,
                startColumn: 7,
                endColumn: 8,
            ),
        );

        $this->testObject->add($location1);
        $this->testObject->add($location2);

        $this->testObject->rewind();

        $this->assertTrue($this->testObject->valid());
        $this->testObject->next();
        $this->assertTrue($this->testObject->valid());
        $this->testObject->next();
        $this->assertFalse($this->testObject->valid());
    }

    public function testRewind()
    {
        $location1 = new Location(
            message: 'test location1',
            filePath: '/src/file1',
            textRange: new Location\TextRange(
                startLine: 1,
                endLine: 2,
                startColumn: 3,
                endColumn: 4,
            ),
        );

        $location2 = new Location(
            message: 'test location2',
            filePath: '/src/file2',
            textRange: new Location\TextRange(
                startLine: 5,
                endLine: 6,
                startColumn: 7,
                endColumn: 8,
            ),
        );

        $this->testObject->add($location1);
        $this->testObject->add($location2);
        $this->testObject->rewind();
        $this->assertEquals(0, $this->testObject->key());
        $this->testObject->next();
        $this->assertEquals(1, $this->testObject->key());
        $this->testObject->rewind();
        $this->assertEquals(0, $this->testObject->key());
    }
}
