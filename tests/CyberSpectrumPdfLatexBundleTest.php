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
            ->setMethods(['addCompilerPass'])
            ->getMock();

        $container
            ->expects($this->exactly(2))
            ->method('addCompilerPass')->willReturnOnConsecutiveCalls(
                $this->returnCallback(function ($pass) use ($container) {
                    $this->assertInstanceOf(SetAutoescapePass::class, $pass);
                    return $container;
                }),
                $this->returnCallback(function ($pass) use ($container) {
                    $this->assertInstanceOf(AddEscaperPass::class, $pass);
                    return $container;
                })
            );

        $bundle->build($container);
    }
}
