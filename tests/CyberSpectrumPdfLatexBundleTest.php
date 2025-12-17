<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\Test;

use CyberSpectrum\PdfLatexBundle\CyberSpectrumPdfLatexBundle;
use CyberSpectrum\PdfLatexBundle\DependencyInjection\Compiler\AddEscaperPass;
use CyberSpectrum\PdfLatexBundle\DependencyInjection\Compiler\SetAutoescapePass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This tests the bundle class.
 *
 * @covers \CyberSpectrum\PdfLatexBundle\CyberSpectrumPdfLatexBundle
 */
class CyberSpectrumPdfLatexBundleTest extends TestCase
{
    /** Test that the class can be instantiated. */
    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\CyberSpectrumPdfLatexBundle',
            $bundle = new CyberSpectrumPdfLatexBundle()
        );
        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\DependencyInjection\PdfLatexExtension',
            $bundle->getContainerExtension()
        );
    }

    /** Test that the compiler passes are registered. */
    public function testRegistersCompilerPasses(): void
    {
        $bundle = new CyberSpectrumPdfLatexBundle();

        $container = $this
            ->getMockBuilder(ContainerBuilder::class)
            ->onlyMethods(['addCompilerPass'])
            ->getMock();

        $container
            ->expects($this->exactly(2))
            ->method('addCompilerPass')->willReturnCallback(
                function ($pass) use ($container) {
                    static $invocation = 0;
                    self::assertInstanceOf(
                        match ($invocation++) {
                            0 => SetAutoescapePass::class,
                            1 => AddEscaperPass::class,
                            default => throw new \LogicException('Was not expected to be called again.'),
                        },
                        $pass
                    );
                    return $container;
                }
            );

        $bundle->build($container);
    }
}
