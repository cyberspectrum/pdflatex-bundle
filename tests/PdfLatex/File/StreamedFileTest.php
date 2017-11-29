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

namespace CyberSpectrum\PdfLatexBundle\Test\PdfLatex\File;

use CyberSpectrum\PdfLatexBundle\PdfLatex\File\StreamedFile;
use CyberSpectrum\PdfLatexBundle\Test\TempDirTestCase;

/**
 * This tests the streamed file class.
 */
class StreamedFileTest extends TempDirTestCase
{
    /**
     * Test that the class can be instantiated.
     *
     * @return void
     */
    public function testCanBeInstantiated()
    {
        $stream = \fopen('php://temp', 'r');

        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\PdfLatex\File\StreamedFile',
            new StreamedFile($stream, 'foo.tex')
        );
        \fclose($stream);
    }

    /**
     * Test that the instantiation throws an exception for invalid resource argument.
     *
     * @return void
     */
    public function testInstantiationThrowsExceptionForNonResource()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument must be a valid resource type. boolean given.');

        new StreamedFile(false, 'foo.tex');
    }

    /**
     * Test that the name is returned.
     *
     * @return void
     */
    public function testNameIsReturned()
    {
        $file = new StreamedFile($stream = \fopen('php://temp', 'r'), 'foo.tex');
        $this->assertSame('foo.tex', $file->getName());

        \fclose($stream);
    }

    /**
     * Test that the directory is returned.
     *
     * @return void
     */
    public function testDirectoryIsReturned()
    {
        $file = new StreamedFile($stream = \fopen('php://temp', 'r'), 'foo.tex', 'subdir');
        $this->assertSame('subdir', $file->getDirectory());

        \fclose($stream);
    }

    /**
     * Test that the name is returned.
     *
     * @return void
     */
    public function testSavesContentToDestination()
    {
        $stream = \fopen('php://temp', 'w+');
        fwrite($stream, 'TESTING!');

        $file = new StreamedFile($stream, 'foo.tex', 'subdir');

        $destDir  = $this->getTempDir();
        $destFile = $destDir . DIRECTORY_SEPARATOR . 'subdir/foo.tex';
        $this->assertFileNotExists($destFile);

        $file->saveTo($destDir);

        $this->assertFileExists($destFile);
        $this->assertSame('TESTING!', \file_get_contents($destFile));

        fclose($stream);
    }
}
