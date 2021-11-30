<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\Test\PdfLatex;

use CyberSpectrum\PdfLatexBundle\PdfLatex\Executor;
use CyberSpectrum\PdfLatexBundle\PdfLatex\ExecutorFactory;
use CyberSpectrum\PdfLatexBundle\Test\TempDirTestCase;

/**
 * This tests the ExecutorFactory.
 *
 * @covers \CyberSpectrum\PdfLatexBundle\PdfLatex\ExecutorFactory
 */
class ExecutorFactoryTest extends TempDirTestCase
{
    /** Test that the class can be instantiated. */
    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\PdfLatex\ExecutorFactory',
            new ExecutorFactory('/bin/false')
        );
    }

    /** Test create an executor. */
    public function testCreatesExecutor(): void
    {
        $tmpDir = $this->getTempDir();
        touch($tmpDir . DIRECTORY_SEPARATOR . 'foo.tex');

        $factory = new ExecutorFactory('/bin/false');
        $this->assertInstanceOf(
            Executor::class,
            $factory->createExecutor($tmpDir, 'foo.tex')
        );
    }
}
