<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\Test\PdfLatex;

use CyberSpectrum\PdfLatexBundle\PdfLatex\File\FileInterface;
use CyberSpectrum\PdfLatexBundle\PdfLatex\Job;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * This tests the PdfLatexJob.
 *
 * @covers \CyberSpectrum\PdfLatexBundle\PdfLatex\Job
 */
class JobTest extends TestCase
{
    /** Test that the class can be instantiated. */
    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\PdfLatex\Job',
            new Job($this->mockTexFile())
        );
    }

    /** Test that the class can be instantiated. */
    public function testInstantiationThrowsExceptionForNonTexFile(): void
    {
        $mock = $this->getMockForAbstractClass(FileInterface::class);
        $mock->method('getName')->willReturn('foo.bar');

        $this->expectException('File foo.bar does not have file extension ".tex"');
        $this->expectException(InvalidArgumentException::class);

        new Job($mock);
    }

    /** Test that the getter returns the input file. */
    public function testTexFileIsReturned(): void
    {
        $mock = $this->mockTexFile();
        $job  = new Job($mock);

        $this->assertSame($mock, $job->getTexFile());
    }

    /** Test that the assets array is empty when none have been added. */
    public function testEmptyAssetsArrayWhenNoneAdded(): void
    {
        $job = new Job($this->mockTexFile());

        $this->assertSame([], $job->getAssets());
    }

    /** Test that assets can be added and retrieved. */
    public function testCanAddAndRetrieveAssets(): void
    {
        $job    = new Job($this->mockTexFile());
        $assets = [$this->mockTexFile(), $this->mockTexFile()];

        $this->assertSame($job, $job->addAsset($assets[0]));
        $this->assertSame($job, $job->addAsset($assets[1]));

        $this->assertSame($assets, $job->getAssets());
    }

    public function testCanAddAndRetrieveIncludePaths(): void
    {
        $job    = new Job($this->mockTexFile());

        $this->assertSame($job, $job->addIncludePath(__DIR__));

        $this->assertSame([__DIR__], $job->getIncludePaths());
    }

    public function testCanAddAndRetrieveIncludePathsForRecursiveSearch(): void
    {
        $job    = new Job($this->mockTexFile());

        $this->assertSame($job, $job->addIncludePath(__DIR__ . '/..//'));

        $this->assertSame([realpath(__DIR__ . '/..') . '//'], $job->getIncludePaths());
    }

    /** Mock a .tex file. */
    private function mockTexFile(): FileInterface
    {
        $mock = $this->getMockForAbstractClass(FileInterface::class);
        $mock->method('getName')->willReturn('foo.tex');

        return $mock;
    }
}
