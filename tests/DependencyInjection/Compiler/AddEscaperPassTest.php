<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\Test\DependencyInjection\Compiler;

use CyberSpectrum\PdfLatexBundle\DependencyInjection\Compiler\AddEscaperPass;
use CyberSpectrum\PdfLatexBundle\Twig\Extension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This tests the compiler pass.
 *
 * @covers \CyberSpectrum\PdfLatexBundle\DependencyInjection\Compiler\AddEscaperPass
 */
class AddEscaperPassTest extends TestCase
{
    /** Test that the compiler pass can be instantiated. */
    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\DependencyInjection\Compiler\AddEscaperPass',
            new AddEscaperPass()
        );
    }

    /** Test that the compiler pass can be instantiated. */
    public function testAbstainsWithoutTwig(): void
    {
        $container = $this
            ->getMockBuilder(ContainerBuilder::class)
            ->onlyMethods(['getDefinition'])
            ->getMock();

        $container
            ->expects($this->never())
            ->method('getDefinition');

        $pass = new AddEscaperPass();

        $pass->process($container);
    }

    /** Test that the compiler pass can be instantiated. */
    public function testInjectsWhenTwigAvailable(): void
    {
        $twig = new Definition('\Twig\Environment', ['loader', ['autoescape' => 'name']]);

        $extension = new Definition(Extension::class, []);

        $container = new ContainerBuilder();
        $container->setDefinition('twig', $twig);
        $container->setDefinition(Extension::class, $extension);

        $pass = new AddEscaperPass();

        $pass->process($container);

        $calls = $extension->getMethodCalls();
        $this->assertSame(1, count($calls));
        $this->assertIsArray($calls[0]);
        $this->assertSame(2, count($calls[0]));
        $this->assertSame('addEscaperTo', $calls[0][0]);
        $this->assertIsArray($calls[0][1]);
        $this->assertSame(1, count($calls[0][1]));
        $this->assertInstanceOf(Reference::class, $calls[0][1][0]);
        $this->assertSame('twig', (string) $calls[0][1][0]);
    }
}
