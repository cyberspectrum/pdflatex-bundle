<?php

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

    /** Test that tex is returned for tex files. */
    public function testReturnsTexForTexFiles(): void
    {
        $strategy = new FileExtensionEscapingStrategy(false);
        $this->assertSame('tex', $strategy->guess('foo.tex.twig'));
        $this->assertSame('tex', $strategy->guess('foo.tex'));
    }

    /** Test that html is returned for directories. */
    public function testReturnsHtmlForDirectories(): void
    {
        $strategy = new FileExtensionEscapingStrategy(false);
        $this->assertSame('html', $strategy->guess('foo\\'));
        $this->assertSame('html', $strategy->guess('foo/'));
    }

    /** Test that false is returned for non tex files. */
    public function testReturnsFalseWhenDefaultIsFalse(): void
    {
        $strategy = new FileExtensionEscapingStrategy(false);
        $this->assertSame(false, $strategy->guess('foo.html.twig'));
    }

    /** Test that string is returned for non tex files. */
    public function testReturnsStringWhenDefaultIsString(): void
    {
        $strategy = new FileExtensionEscapingStrategy('string');
        $this->assertSame('string', $strategy->guess('foo.html.twig'));
    }

    /** Test that callable is called for non tex files. */
    public function testReturnsCallableResultWhenDefaultIsCallable(): void
    {
        $strategy = new FileExtensionEscapingStrategy(function (string $name): string {
            $this->assertSame('foo.html.twig', $name);
            return 'foo';
        });
        $this->assertSame('foo', $strategy->guess('foo.html.twig'));
    }
}
