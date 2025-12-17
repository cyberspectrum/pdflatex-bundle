<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\Test\PdfLatex;

use CyberSpectrum\PdfLatexBundle\PdfLatex\Executor;
use CyberSpectrum\PdfLatexBundle\PdfLatex\ExecutorFactory;
use CyberSpectrum\PdfLatexBundle\PdfLatex\ExecutorFactoryInterface;
use CyberSpectrum\PdfLatexBundle\PdfLatex\ExecutorInterface;
use CyberSpectrum\PdfLatexBundle\PdfLatex\File\FileInterface;
use CyberSpectrum\PdfLatexBundle\PdfLatex\Job;
use CyberSpectrum\PdfLatexBundle\PdfLatex\JobProcessor;
use PHPUnit\Framework\TestCase;

/**
 * This tests the JobProcessor class.
 *
 * @covers \CyberSpectrum\PdfLatexBundle\PdfLatex\JobProcessor
 */
class JobProcessorTest extends TestCase
{
    /** Test that the class can be instantiated. */
    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\PdfLatex\JobProcessor',
            new JobProcessor(
                $this->getMockBuilder(ExecutorFactoryInterface::class)->disableOriginalConstructor()->getMock(),
                '/working/base/dir'
            )
        );
    }

    /** Test the processing of a job. */
    public function testProcessing(): void
    {
        $executor = $this
            ->getMockBuilder(ExecutorInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['run'])
            ->getMock();

        $executor
            ->expects($this->once())
            ->method('run')
            ->with()
            ->willReturn('/working/base/dir/jobdir/foo.pdf');

        $factory = $this
            ->getMockBuilder(ExecutorFactoryInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['createExecutor'])
            ->getMock();

        $factory
            ->expects($this->once())
            ->method('createExecutor')
            ->with()
            ->willReturn($executor);

        $file   = $this->mockFile('foo.tex', '', '/working/base/dir/job1');
        $asset1 = $this->mockFile('bar.tex', 'assets', '/working/base/dir/job1');
        $asset2 = $this->mockFile('asset.tex', '', '/working/base/dir/job1');

        $job = new Job($file, 'job1');
        $job->addAsset($asset1);
        $job->addAsset($asset2);

        $processor = new JobProcessor($factory, '/working/base/dir');

        $this->assertSame('/working/base/dir/jobdir/foo.pdf', $processor->process($job));
    }

    /**
     * Mock a file.
     *
     * @param string $fileName The file name.
     * @param string $subDir   The subdirectory.
     * @param string $saveDir  The destination directory.
     *
     * @return FileInterface
     */
    private function mockFile(string $fileName, string $subDir, string $saveDir): FileInterface
    {
        $mock = $this->getMockBuilder(FileInterface::class)->getMock();
        $mock->method('getName')->willReturn($fileName);
        $mock->method('getDirectory')->willReturn($subDir);
        $mock->expects($this->once())->method('saveTo')->with($saveDir);

        return $mock;
    }
}
