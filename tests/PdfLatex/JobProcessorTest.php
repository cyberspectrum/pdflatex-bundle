<?php

/**
 * This file is part of cyberspectrum/pdflatex-bundle.
 *
 * (c) CyberSpectrum <http://www.cyberspectrum.de/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    cyberspectrum/pdflatex-bundle
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright  2017 CyberSpectrum <http://www.cyberspectrum.de/>
 * @license    LGPL https://github.com/cyberspectrum/pdflatex-bundle/blob/master/LICENSE
 * @filesource
 */
declare(strict_types = 1);

namespace CyberSpectrum\PdfLatexBundle\Test\PdfLatex;

use CyberSpectrum\PdfLatexBundle\PdfLatex\Executor;
use CyberSpectrum\PdfLatexBundle\PdfLatex\ExecutorFactory;
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
    /**
     * Test that the class can be instantiated.
     *
     * @return void
     */
    public function testCanBeInstantiated()
    {
        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\PdfLatex\JobProcessor',
            new JobProcessor(
                $this->getMockBuilder(ExecutorFactory::class)->disableOriginalConstructor()->getMock(),
                '/working/base/dir'
            )
        );
    }

    /**
     * Test the processing of a job.
     *
     * @return void
     */
    public function testProcessing()
    {
        $executor = $this
            ->getMockBuilder(Executor::class)
            ->disableOriginalConstructor()
            ->setMethods(['run'])
            ->getMock();

        $executor
            ->expects($this->once())
            ->method('run')
            ->with()
            ->willReturn('/working/base/dir/jobdir/foo.pdf');

        $factory = $this
            ->getMockBuilder(ExecutorFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['createExecutor'])
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

        /** @var JobProcessor $processor */
        $this->assertSame('/working/base/dir/jobdir/foo.pdf', $processor->process($job));
    }

    /**
     * Mock a file.
     *
     * @param string $fileName The file name.
     * @param string $subDir   The sub directory.
     * @param string $saveDir  The destination directory.
     *
     * @return FileInterface
     */
    private function mockFile($fileName, $subDir, $saveDir): FileInterface
    {
        $mock = $this->getMockForAbstractClass(FileInterface::class);
        $mock->method('getName')->willReturn($fileName);
        $mock->method('getDirectory')->willReturn($subDir);
        $mock->expects($this->once())->method('saveTo')->with($saveDir);

        return $mock;
    }
}
