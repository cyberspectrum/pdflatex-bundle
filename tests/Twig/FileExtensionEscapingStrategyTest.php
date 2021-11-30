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

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\Test\Twig;

use CyberSpectrum\PdfLatexBundle\Twig\FileExtensionEscapingStrategy;
use PHPUnit\Framework\TestCase;

/**
 * This tests the file extension escaping strategy.
 */
class FileExtensionEscapingStrategyTest extends TestCase
{
    /**
     * Test that the class can be instantiated.
     *
     * @return void
     */
    public function testCanBeInstantiated()
    {
        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\Twig\FileExtensionEscapingStrategy',
            new FileExtensionEscapingStrategy(false)
        );
    }

    /**
     * Test that tex is returned for tex files.
     *
     * @return void
     */
    public function testReturnsTexForTexFiles()
    {
        $strategy = new FileExtensionEscapingStrategy(false);
        $this->assertSame('tex', $strategy->guess('foo.tex.twig'));
        $this->assertSame('tex', $strategy->guess('foo.tex'));
    }

    /**
     * Test that html is returned for directories.
     *
     * @return void
     */
    public function testReturnsHtmlForDirectories()
    {
        $strategy = new FileExtensionEscapingStrategy(false);
        $this->assertSame('html', $strategy->guess('foo\\'));
        $this->assertSame('html', $strategy->guess('foo/'));
    }

    /**
     * Test that false is returned for non tex files.
     *
     * @return void
     */
    public function testReturnsFalseWhenDefaultIsFalse()
    {
        $strategy = new FileExtensionEscapingStrategy(false);
        $this->assertSame(false, $strategy->guess('foo.html.twig'));
    }

    /**
     * Test that string is returned for non tex files.
     *
     * @return void
     */
    public function testReturnsStringWhenDefaultIsString()
    {
        $strategy = new FileExtensionEscapingStrategy('string');
        $this->assertSame('string', $strategy->guess('foo.html.twig'));
    }

    /**
     * Test that callable is called for non tex files.
     *
     * @return void
     */
    public function testReturnsCallableResultWhenDefaultIsCallable()
    {
        $strategy = new FileExtensionEscapingStrategy(function ($name) {
            $this->assertSame('foo.html.twig', $name);
            return 'foo';
        });
        $this->assertSame('foo', $strategy->guess('foo.html.twig'));
    }
}
