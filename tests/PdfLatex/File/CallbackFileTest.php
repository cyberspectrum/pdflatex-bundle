<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\Test\PdfLatex\File;

use CyberSpectrum\PdfLatexBundle\PdfLatex\File\CallbackFile;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * This tests the CallbackFile class.
 *
 * @covers \CyberSpectrum\PdfLatexBundle\PdfLatex\File\CallbackFile
 */
class CallbackFileTest extends TestCase
{
    /** Test that the class can be instantiated. */
    public function testCanBeInstantiated(): void
    {
        $callback = function () {
            Assert::fail('Callback should not have been called');
        };

        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\PdfLatex\File\CallbackFile',
            new CallbackFile($callback, 'foo.tex')
        );
    }

    /** Test that the name is returned. */
    public function testNameIsReturned(): void
    {
        $callback = function () {
            Assert::fail('Callback should not have been called');
        };

        $file = new CallbackFile($callback, 'foo.tex');
        $this->assertSame('foo.tex', $file->getName());
    }

    /** Test that the directory is returned. */
    public function testDirectoryIsReturned(): void
    {
        $callback = function () {
            Assert::fail('Callback should not have been called');
        };

        $file = new CallbackFile($callback, 'foo.tex', 'subdir');

        $this->assertSame('subdir', $file->getDirectory());
    }

    /** Test that the name is returned. */
    public function testSavesContentToDestination(): void
    {
        $callback = function ($directory, $name) {
            Assert::assertSame('destdir' . DIRECTORY_SEPARATOR . 'subdir', $directory);
            Assert::assertSame('foo.tex', $name);
        };

        $file = new CallbackFile($callback, 'foo.tex', 'subdir');

        $file->saveTo('destdir');
    }
}
