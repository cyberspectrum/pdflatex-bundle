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

use CyberSpectrum\PdfLatexBundle\Exception\LatexFailedException;
use CyberSpectrum\PdfLatexBundle\PdfLatex\Executor;
use CyberSpectrum\PdfLatexBundle\Test\TempDirTestCase;
use Symfony\Component\Process\ExecutableFinder;

/**
 * This tests the PdfLatexExecutor class.
 */
class ExecutorTest extends TempDirTestCase
{
    /**
     * Test that the class can be instantiated.
     *
     * @return void
     */
    public function testCanBeInstantiated()
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

    /**
     * Test that an exception is thrown when the binary does not exist.
     *
     * @return void
     */
    public function testInstantiationThrowsExceptionForNonExistentBinary()
    {
        $tmpDir = $this->getTempDir();
        touch($tmpDir . DIRECTORY_SEPARATOR . 'foo.tex');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('File ' . $tmpDir . '/does/not/exist is not executable.');

        new Executor($tmpDir . '/does/not/exist', $tmpDir, 'foo.tex', []);
    }

    /**
     * Test that an exception is thrown when the tex file has an invalid file extension.
     *
     * @return void
     */
    public function testInstantiationThrowsExceptionForInvalidTexFileExtension()
    {
        $tmpDir = $this->getTempDir();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('File foo.bar does not have file extension ".tex".');

        new Executor('/bin/false', $tmpDir, 'foo.bar', []);
    }

    /**
     * Test that an exception is thrown when the tex file does not exist.
     *
     * @return void
     */
    public function testInstantiationThrowsExceptionForNonExistentTexFile()
    {
        $tmpDir = $this->getTempDir();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('File ' . $tmpDir . '/foo.tex does not exist.');

        new Executor('/bin/false', $tmpDir, 'foo.tex', []);
    }

    /**
     * Test the execution.
     *
     * @return void
     */
    public function testRunThrowsExceptionWhenExecutionFailed()
    {
        $tmpDir = $this->getTempDir();
        touch($tmpDir . DIRECTORY_SEPARATOR . 'foo.tex');

        $executor = new Executor('/bin/false', $tmpDir, 'foo.tex', []);

        $this->expectException(LatexFailedException::class);

        $executor->run();
    }

    /**
     * Test the execution.
     *
     * @return void
     */
    public function testRunWithRealPdflatex()
    {
        $finder = new ExecutableFinder();

        if (null === ($binary = $finder->find('pdflatex'))) {
            $this->markTestSkipped('Could not find pdflatex');

            return;
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

    /**
     * Test the execution.
     *
     * @return void
     */
    public function testExecutorSetsEnvironment()
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
            'TEXINPUTS=/include/path' . PATH_SEPARATOR . '/include/path2',
            $contents[0]
        );
        $this->assertSame(
            '-halt-on-error -output-directory=' . $tmpDir . ' -interaction=nonstopmode foo.tex',
            $contents[1]
        );
    }
}
