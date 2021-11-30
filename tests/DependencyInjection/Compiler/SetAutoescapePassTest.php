<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\Test\DependencyInjection\Compiler;

use CyberSpectrum\PdfLatexBundle\DependencyInjection\Compiler\SetAutoescapePass;
use CyberSpectrum\PdfLatexBundle\Twig\FileExtensionEscapingStrategy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This tests the compiler pass.
 *
 * @covers \CyberSpectrum\PdfLatexBundle\DependencyInjection\Compiler\SetAutoescapePass
 */
class SetAutoescapePassTest extends TestCase
{
    /** Test that the compiler pass can be instantiated. */
    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\DependencyInjection\Compiler\SetAutoescapePass',
            new SetAutoescapePass()
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

        $pass = new SetAutoescapePass();

        $pass->process($container);
    }

    /** Test that the compiler pass can be instantiated. */
    public function testAbstainsWhenAutoescapeIsString(): void
    {
        $twig = new Definition('\Twig\Class', ['loader', ['autoescape' => 'html']]);

        $container = new ContainerBuilder();
        $container->setDefinition('twig', $twig);

        $pass = new SetAutoescapePass();

        $pass->process($container);

        $this->assertSame(['autoescape' => 'html'], $twig->getArgument(1));
    }

    /** Test that the compiler pass can be instantiated. */
    public function testInjectsWhenAutoescapeIsName(): void
    {
        $twig = new Definition('\Twig\Environment', ['loader', ['autoescape' => 'name']]);

        $strategy = new Definition(FileExtensionEscapingStrategy::class, [false]);

        $container = new ContainerBuilder();
        $container->setDefinition('twig', $twig);
        $container->setDefinition('cyberspectrum.pdflatex.twig.file_extension_escaping_strategy', $strategy);

        $pass = new SetAutoescapePass();

        $pass->process($container);

        $options = $twig->getArgument(1);

        $this->assertInstanceOf(Reference::class, $options['autoescape'][0]);
        $this->assertSame(
            'cyberspectrum.pdflatex.twig.file_extension_escaping_strategy',
            (string) $options['autoescape'][0]
        );
        $this->assertSame('guess', $options['autoescape'][1]);

        $default = $strategy->getArgument(0);
        $this->assertSame(['\Twig\FileExtensionEscapingStrategy', 'guess'], $default);
    }

    /** Test that the compiler pass can be instantiated. */
    public function testInjectsWhenAutoescapeIsFalse(): void
    {
        $twig = new Definition('\Twig\Environment', ['loader', ['autoescape' => false]]);

        $strategy = new Definition(FileExtensionEscapingStrategy::class, ['something']);

        $container = new ContainerBuilder();
        $container->setDefinition('twig', $twig);
        $container->setDefinition('cyberspectrum.pdflatex.twig.file_extension_escaping_strategy', $strategy);

        $pass = new SetAutoescapePass();

        $pass->process($container);

        $options = $twig->getArgument(1);

        $this->assertInstanceOf(Reference::class, $options['autoescape'][0]);
        $this->assertSame(
            'cyberspectrum.pdflatex.twig.file_extension_escaping_strategy',
            (string) $options['autoescape'][0]
        );
        $this->assertSame('guess', $options['autoescape'][1]);

        $default = $strategy->getArgument(0);
        $this->assertSame(false, $default);
    }

    /** Test that the compiler pass can be instantiated. */
    public function testInjectsWhenAutoescapeIsService(): void
    {
        $realStrategy = [new Reference('foo.bar'), 'baz'];

        $twig = new Definition('\Twig\Environment', ['loader', ['autoescape' => $realStrategy]]);

        $strategy = new Definition(FileExtensionEscapingStrategy::class, [false]);

        $container = new ContainerBuilder();
        $container->setDefinition('twig', $twig);
        $container->setDefinition('cyberspectrum.pdflatex.twig.file_extension_escaping_strategy', $strategy);

        $pass = new SetAutoescapePass();

        $pass->process($container);

        $options = $twig->getArgument(1);

        $this->assertInstanceOf(Reference::class, $options['autoescape'][0]);
        $this->assertSame(
            'cyberspectrum.pdflatex.twig.file_extension_escaping_strategy',
            (string) $options['autoescape'][0]
        );
        $this->assertSame('guess', $options['autoescape'][1]);

        $default = $strategy->getArgument(0);
        $this->assertSame($realStrategy, $default);
    }
}
