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

use CyberSpectrum\PdfLatexBundle\PdfLatex\File\PhysicalFile;
use CyberSpectrum\PdfLatexBundle\Test\TempDirTestCase;

/**
 * This tests the PhysicalFile class.
 */
class PhysicalFileTest extends TempDirTestCase
{
    /**
     * Test that the class can be instantiated.
     *
     * @return void
     */
    public function testCanBeInstantiated()
    {
        touch($filePath = $this->getTempDir() . DIRECTORY_SEPARATOR . 'foo.tex');

        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\PdfLatex\File\PhysicalFile',
            new PhysicalFile($filePath)
        );
    }

    /**
     * Test that the instantiation throws an exception for invalid file path argument.
     *
     * @return void
     */
    public function testInstantiationThrowsExceptionForNonExistingSourceFile()
    {
        $filePath = $this->getTempDir() . DIRECTORY_SEPARATOR . 'foo.tex';
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('File ' . $filePath . ' does not exist.');

        new PhysicalFile($filePath, 'foo.tex');
    }

    /**
     * Test that the name is returned.
     *
     * @return void
     */
    public function testNameIsReturned()
    {
        touch($filePath = $this->getTempDir() . DIRECTORY_SEPARATOR . 'foo.tex');

        $file = new PhysicalFile($filePath);
        $this->assertSame('foo.tex', $file->getName());
    }

    /**
     * Test that the directory is returned.
     *
     * @return void
     */
    public function testDirectoryIsReturned()
    {
        touch($filePath = $this->getTempDir() . DIRECTORY_SEPARATOR . 'foo.tex');

        $file = new PhysicalFile($filePath, 'subdir');
        $this->assertSame('subdir', $file->getDirectory());
    }

    /**
     * Test that the name is returned.
     *
     * @return void
     */
    public function testSavesContentToDestination()
    {
        file_put_contents($filePath = $this->getTempDir() . DIRECTORY_SEPARATOR . 'foo.tex', 'TESTING!');

        $file = new PhysicalFile($filePath, 'subdir');

        $destDir  = $this->getTempDir();
        $destFile = $destDir . DIRECTORY_SEPARATOR . 'subdir/foo.tex';
        $this->assertFileNotExists($destFile);

        $file->saveTo($destDir);

        $this->assertFileExists($destFile);
        $this->assertSame('TESTING!', \file_get_contents($destFile));
    }
}
