<?php
declare(strict_types=1);

namespace Powercloud\SRT\Tests\DomainModel\Input\PhpcsReport;

use PHPUnit\Framework\TestCase;
use Powercloud\SRT\DomainModel\Input\PhpcsReport\File;
use Powercloud\SRT\DomainModel\Input\PhpcsReport\FileCollection;

class FileCollectionTest extends TestCase
{
    private File $file1;
    private File $file2;
    private FileCollection $testObject;

    public function setUp(): void
    {
        $this->file1 = new File(
            name: 'file1',
            errors: 1,
            warnings: 2,
            messages: new File\MessageCollection()
        );

        $this->file2 = new File(
            name: 'file2',
            errors: 3,
            warnings: 4,
            messages: new File\MessageCollection()
        );

        $this->testObject = new FileCollection();
    }

    public function testAddWithWrongTypeThrowsErrorException(): void
    {
        $this->expectError();
        $this->expectErrorMessage(
            sprintf(
                'Argument #1 ($file) must be of type %s, class@anonymous given',
                File::class
            )
        );

        $this->testObject->add(new class{});
    }

    public function testAddMultiple(): void
    {
        $this->testObject->add($this->file1);
        $this->testObject->add($this->file2);

        $this->testObject->rewind();
        $this->assertSame($this->file1, $this->testObject->current());
        $this->testObject->next();
        $this->assertSame($this->file2, $this->testObject->current());
    }

    public function testCurrentAndNext()
    {
        $this->testObject->add($this->file1);
        $this->testObject->add($this->file2);
        $this->testObject->rewind();

        $this->assertSame($this->file1, $this->testObject->current());

        $this->testObject->next();

        $this->assertSame($this->file2, $this->testObject->current());
    }
}
