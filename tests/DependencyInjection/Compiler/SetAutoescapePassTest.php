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
declare(strict_types = 1);

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
    /**
     * Test that the compiler pass can be instantiated.
     *
     * @return void
     */
    public function testCanBeInstantiated()
    {
        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\DependencyInjection\Compiler\SetAutoescapePass',
            new SetAutoescapePass()
        );
    }

    /**
     * Test that the compiler pass can be instantiated.
     *
     * @return void
     */
    public function testAbstainsWithoutTwig()
    {
        $container = $this
            ->getMockBuilder(ContainerBuilder::class)
            ->setMethods(['getDefinition'])
            ->getMock();

        $container
            ->expects($this->never())
            ->method('getDefinition');

        $pass = new SetAutoescapePass();

        $pass->process($container);
    }

    /**
     * Test that the compiler pass can be instantiated.
     *
     * @return void
     */
    public function testAbstainsWhenAutoescapeIsString()
    {
        $twig = new Definition('\Twig\Class', ['loader', ['autoescape' => 'html']]);

        $container = new ContainerBuilder();
        $container->setDefinition('twig', $twig);

        $pass = new SetAutoescapePass();

        $pass->process($container);

        $this->assertSame(['autoescape' => 'html'], $twig->getArgument(1));
    }

    /**
     * Test that the compiler pass can be instantiated.
     *
     * @return void
     */
    public function testInjectsWhenAutoescapeIsName()
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

    /**
     * Test that the compiler pass can be instantiated.
     *
     * @return void
     */
    public function testInjectsWhenAutoescapeIsFalse()
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

    /**
     * Test that the compiler pass can be instantiated.
     *
     * @return void
     */
    public function testInjectsWhenAutoescapeIsService()
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
