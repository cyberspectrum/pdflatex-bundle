<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\Test\PdfLatex\File;

use CyberSpectrum\PdfLatexBundle\PdfLatex\File\StreamedFile;
use CyberSpectrum\PdfLatexBundle\Test\TempDirTestCase;
use InvalidArgumentException;

use function fclose;
use function file_get_contents;
use function fopen;

/**
 * This tests the streamed file class.
 *
 * @covers \CyberSpectrum\PdfLatexBundle\PdfLatex\File\StreamedFile
 */
class StreamedFileTest extends TempDirTestCase
{
    /** Test that the class can be instantiated. */
    public function testCanBeInstantiated(): void
    {
        $stream = fopen('php://temp', 'r');

        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\PdfLatex\File\StreamedFile',
            new StreamedFile($stream, 'foo.tex')
        );
        fclose($stream);
    }

    /** Test that the instantiation throws an exception for invalid resource argument. */
    public function testInstantiationThrowsExceptionForNonResource(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument must be a valid resource type. boolean given.');

        new StreamedFile(false, 'foo.tex');
    }

    /** Test that the name is returned. */
    public function testNameIsReturned(): void
    {
        $file = new StreamedFile($stream = fopen('php://temp', 'r'), 'foo.tex');
        $this->assertSame('foo.tex', $file->getName());

        fclose($stream);
    }

    /** Test that the directory is returned. */
    public function testDirectoryIsReturned(): void
    {
        $file = new StreamedFile($stream = fopen('php://temp', 'r'), 'foo.tex', 'subdir');
        $this->assertSame('subdir', $file->getDirectory());

        fclose($stream);
    }

    /** Test that the name is returned. */
    public function testSavesContentToDestination(): void
    {
        $stream = fopen('php://temp', 'w+');
        fwrite($stream, 'TESTING!');

        $file = new StreamedFile($stream, 'foo.tex', 'subdir');

        $destDir  = $this->getTempDir();
        $destFile = $destDir . DIRECTORY_SEPARATOR . 'subdir/foo.tex';
        $this->assertFileDoesNotExist($destFile);

        $file->saveTo($destDir);

        $this->assertFileExists($destFile);
        $this->assertSame('TESTING!', file_get_contents($destFile));

        fclose($stream);
    }
}
