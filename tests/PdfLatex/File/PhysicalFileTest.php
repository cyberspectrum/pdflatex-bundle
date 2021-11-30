<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\Test\PdfLatex\File;

use CyberSpectrum\PdfLatexBundle\PdfLatex\File\PhysicalFile;
use CyberSpectrum\PdfLatexBundle\Test\TempDirTestCase;
use InvalidArgumentException;

use function file_get_contents;
use function file_put_contents;
use function touch;

/**
 * This tests the PhysicalFile class.
 *
 * @covers \CyberSpectrum\PdfLatexBundle\PdfLatex\File\PhysicalFile
 */
class PhysicalFileTest extends TempDirTestCase
{
    /** Test that the class can be instantiated. */
    public function testCanBeInstantiated(): void
    {
        touch($filePath = $this->getTempDir() . DIRECTORY_SEPARATOR . 'foo.tex');

        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\PdfLatex\File\PhysicalFile',
            new PhysicalFile($filePath)
        );
    }

    /** Test that the instantiation throws an exception for invalid file path argument. */
    public function testInstantiationThrowsExceptionForNonExistingSourceFile(): void
    {
        $filePath = $this->getTempDir() . DIRECTORY_SEPARATOR . 'foo.tex';
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('File ' . $filePath . ' does not exist.');

        new PhysicalFile($filePath, 'foo.tex');
    }

    /** Test that the name is returned. */
    public function testNameIsReturned(): void
    {
        touch($filePath = $this->getTempDir() . DIRECTORY_SEPARATOR . 'foo.tex');

        $file = new PhysicalFile($filePath);
        $this->assertSame('foo.tex', $file->getName());
    }

    /** Test that the directory is returned. */
    public function testDirectoryIsReturned(): void
    {
        touch($filePath = $this->getTempDir() . DIRECTORY_SEPARATOR . 'foo.tex');

        $file = new PhysicalFile($filePath, 'subdir');
        $this->assertSame('subdir', $file->getDirectory());
    }

    /** Test that the name is returned. */
    public function testSavesContentToDestination(): void
    {
        file_put_contents($filePath = $this->getTempDir() . DIRECTORY_SEPARATOR . 'foo.tex', 'TESTING!');

        $file = new PhysicalFile($filePath, 'subdir');

        $destDir  = $this->getTempDir();
        $destFile = $destDir . DIRECTORY_SEPARATOR . 'subdir/foo.tex';
        $this->assertFileDoesNotExist($destFile);

        $file->saveTo($destDir);

        $this->assertFileExists($destFile);
        $this->assertSame('TESTING!', file_get_contents($destFile));
    }
}
