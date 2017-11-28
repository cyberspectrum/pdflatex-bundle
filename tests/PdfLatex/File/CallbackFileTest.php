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

declare (strict_types = 1);

namespace CyberSpectrum\PdfLatexBundle\Test\PdfLatex\File;

use CyberSpectrum\PdfLatexBundle\PdfLatex\File\CallbackFile;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * This tests the CallbackFile class.
 */
class CallbackFileTest extends TestCase
{
    /**
     * Test that the class can be instantiated.
     *
     * @return void
     */
    public function testCanBeInstantiated()
    {
        $callback = function () {
            Assert::fail('Callback should not have been called');
        };

        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\PdfLatex\File\CallbackFile',
            new CallbackFile($callback, 'foo.tex')
        );
    }

    /**
     * Test that the name is returned.
     *
     * @return void
     */
    public function testNameIsReturned()
    {
        $callback = function () {
            Assert::fail('Callback should not have been called');
        };

        $file = new CallbackFile($callback, 'foo.tex');
        $this->assertSame('foo.tex', $file->getName());
    }

    /**
     * Test that the directory is returned.
     *
     * @return void
     */
    public function testDirectoryIsReturned()
    {
        $callback = function () {
            Assert::fail('Callback should not have been called');
        };

        $file = new CallbackFile($callback, 'foo.tex', 'subdir');

        $this->assertSame('subdir', $file->getDirectory());
    }

    /**
     * Test that the name is returned.
     *
     * @return void
     */
    public function testSavesContentToDestination()
    {
        $callback = function ($directory, $name, $subdir) {
            Assert::assertSame('destdir', $directory);
            Assert::assertSame('foo.tex', $name);
            Assert::assertSame('subdir', $subdir);
        };

        $file = new CallbackFile($callback, 'foo.tex', 'subdir');

        $file->saveTo('destdir');
    }
}
