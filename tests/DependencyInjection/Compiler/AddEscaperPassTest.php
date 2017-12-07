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
    /**
     * Test that the compiler pass can be instantiated.
     *
     * @return void
     */
    public function testCanBeInstantiated()
    {
        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\DependencyInjection\Compiler\AddEscaperPass',
            new AddEscaperPass()
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

        $pass = new AddEscaperPass();

        $pass->process($container);
    }

    /**
     * Test that the compiler pass can be instantiated.
     *
     * @return void
     */
    public function testInjectsWhenTwigAvailable()
    {
        $twig = new Definition('\Twig\Environment', ['loader', ['autoescape' => 'name']]);

        $extension = new Definition(Extension::class, []);

        $container = new ContainerBuilder();
        $container->setDefinition('twig', $twig);
        $container->setDefinition('cyberspectrum.pdflatex.twig.extension', $extension);

        $pass = new AddEscaperPass();

        $pass->process($container);

        $calls = $extension->getMethodCalls();
        $this->assertSame(1, count($calls));
        $this->assertInternalType('array', $calls[0]);
        $this->assertSame(2, count($calls[0]));
        $this->assertSame('addEscaperTo', $calls[0][0]);
        $this->assertInternalType('array', $calls[0][1]);
        $this->assertSame(1, count($calls[0][1]));
        $this->assertInstanceOf(Reference::class, $calls[0][1][0]);
        $this->assertSame('twig', (string) $calls[0][1][0]);
    }
}
