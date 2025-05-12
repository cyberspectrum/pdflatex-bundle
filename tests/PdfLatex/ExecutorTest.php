<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\Test\PdfLatex;

use CyberSpectrum\PdfLatexBundle\Exception\LatexFailedException;
use CyberSpectrum\PdfLatexBundle\PdfLatex\Executor;
use CyberSpectrum\PdfLatexBundle\Test\TempDirTestCase;
use InvalidArgumentException;
use Symfony\Component\Process\ExecutableFinder;

use function dirname;
use function explode;
use function file_get_contents;
use function file_put_contents;
use function touch;

/**
 * This tests the PdfLatexExecutor class.
 *
 * @covers \CyberSpectrum\PdfLatexBundle\PdfLatex\Executor
 */
class ExecutorTest extends TempDirTestCase
{
    /** Test that the class can be instantiated. */
    public function testCanBeInstantiated(): void
    {
        $tmpDir = $this->getTempDir();
        touch($tmpDir . DIRECTORY_SEPARATOR . 'foo.tex');

        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\PdfLatex\Executor',
            new Executor(
                '/bin/false',
                $tmpDir,
                'foo.tex',
                []
            )
        );
    }

    /** Test that an exception is thrown when the binary does not exist. */
    public function testInstantiationThrowsExceptionForNonExistentBinary(): void
    {
        $tmpDir = $this->getTempDir();
        touch($tmpDir . DIRECTORY_SEPARATOR . 'foo.tex');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('File ' . $tmpDir . '/does/not/exist is not executable.');

        new Executor($tmpDir . '/does/not/exist', $tmpDir, 'foo.tex', []);
    }

    /** Test that an exception is thrown when the tex file has an invalid file extension. */
    public function testInstantiationThrowsExceptionForInvalidTexFileExtension(): void
    {
        $tmpDir = $this->getTempDir();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('File foo.bar does not have file extension ".tex".');

        new Executor('/bin/false', $tmpDir, 'foo.bar', []);
    }

    /** Test that an exception is thrown when the tex file does not exist. */
    public function testInstantiationThrowsExceptionForNonExistentTexFile(): void
    {
        $tmpDir = $this->getTempDir();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('File ' . $tmpDir . '/foo.tex does not exist.');

        new Executor('/bin/false', $tmpDir, 'foo.tex', []);
    }

    /** Test the execution. */
    public function testRunThrowsExceptionWhenExecutionFailed(): void
    {
        $tmpDir = $this->getTempDir();
        touch($tmpDir . DIRECTORY_SEPARATOR . 'foo.tex');

        $executor = new Executor('/bin/false', $tmpDir, 'foo.tex', []);

        $this->expectException(LatexFailedException::class);

        $executor->run();
    }

    /** Test the execution. */
    public function testRunWithRealPdflatex(): void
    {
        $finder = new ExecutableFinder();

        if (null === ($binary = $finder->find('pdflatex'))) {
            $this->markTestSkipped('Could not find pdflatex');
        }

        $tmpDir = $this->getTempDir();
        file_put_contents(
            $tmpDir . DIRECTORY_SEPARATOR . 'foo.tex',
            '\documentclass[12pt]{article}\begin{document}\tableofcontents\section{Test}Test.\end{document}'
        );

        $executor = new Executor($binary, $tmpDir, 'foo.tex', []);

        $this->assertSame($tmpDir . DIRECTORY_SEPARATOR . 'foo.pdf', $executor->run());

        $this->assertFileExists($tmpDir . DIRECTORY_SEPARATOR . 'foo.pdf');
    }

    /** Test the execution. */
    public function testExecutorSetsEnvironment(): void
    {
        $tmpDir = $this->getTempDir();
        file_put_contents(
            $tmpDir . DIRECTORY_SEPARATOR . 'foo.tex',
            '\documentclass[12pt]{article}\begin{document}\tableofcontents\section{Test}Test.\end{document}'
        );

        $executor = new Executor(
            dirname(__DIR__) . '/fixtures/pdflatex',
            $tmpDir,
            'foo.tex',
            ['/include/path', '/include/path2']
        );

        $this->assertSame($tmpDir . DIRECTORY_SEPARATOR . 'foo.pdf', $executor->run());

        $this->assertFileExists($tmpDir . DIRECTORY_SEPARATOR . 'foo.pdf');
        $contents = explode("\n", file_get_contents($tmpDir . DIRECTORY_SEPARATOR . 'foo.pdf'));
        $this->assertSame(
            'TEXINPUTS=/include/path' . PATH_SEPARATOR . '/include/path2' . PATH_SEPARATOR,
            $contents[0]
        );
        $this->assertSame(
            '-halt-on-error -output-directory=' . $tmpDir . ' -interaction=nonstopmode foo.tex',
            $contents[1]
        );
    }
}
