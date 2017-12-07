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

namespace CyberSpectrum\PdfLatexBundle\Test\DependencyInjection;

use CyberSpectrum\PdfLatexBundle\DependencyInjection\PdfLatexExtension;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * This tests the Configuration class.
 *
 * @covers \CyberSpectrum\PdfLatexBundle\DependencyInjection\PdfLatexExtension
 */
class PdfLatexExtensionTest extends TestCase
{
    /**
     * Keep $PATH environment.
     *
     * @var string
     */
    private static $path;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$path = getenv('PATH');
    }

    /**
     * {@inheritdoc}
     */
    public static function tearDownAfterClass()
    {
        putenv('PATH=' . self::$path);
        parent::tearDownAfterClass();
    }

    /**
     * Test that the bundle can be instantiated.
     *
     * @return void
     */
    public function testCanBeInstantiated()
    {
        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\DependencyInjection\PdfLatexExtension',
            $extension = new PdfLatexExtension()
        );
        $this->assertInstanceOf(Extension::class, $extension);
    }

    /**
     * Test that the extension uses path override.
     *
     * @return void
     */
    public function testLoadReturnsOverriddenPathToBinary()
    {
        $container = $this
            ->getMockBuilder(ContainerBuilder::class)
            ->setMethods(['setParameter'])
            ->getMock();

        $container
            ->expects($this->once())
            ->method('setParameter')
            ->with('cyberspectrum.pdflatex.binary', '/bin/false');

        $extension = new PdfLatexExtension();
        $extension->load(
            [
                'cs_pdflatex' => [
                    'pdflatex_binary' => '/bin/false',
                ],
            ],
            $container
        );
    }

    /**
     * Test that the extension uses search path by default.
     *
     * @return void
     */
    public function testLoadReturnsBinaryFromSearchPath()
    {
        $container = $this
            ->getMockBuilder(ContainerBuilder::class)
            ->setMethods(['setParameter'])
            ->getMock();

        putenv('PATH=' . ($dir = dirname(__DIR__) . '/fixtures'));

        $container
            ->expects($this->once())
            ->method('setParameter')
            ->with('cyberspectrum.pdflatex.binary', $dir . '/pdflatex');

        $extension = new PdfLatexExtension();
        $extension->load([], $container);
    }

    /**
     * Test that the extension throws an exception when no pdflatex is found.
     *
     * @return void
     */
    public function testLoadThrowsExceptionWhenNoBinaryFound()
    {
        $container = $this
            ->getMockBuilder(ContainerBuilder::class)
            ->setMethods(['setParameter'])
            ->getMock();

        putenv('PATH=');

        $container
            ->expects($this->never())
            ->method('setParameter');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not find a pdflatex binary.');

        $extension = new PdfLatexExtension();
        $extension->load([], $container);
    }

    /**
     * Test that the services are being registered.
     *
     * @return void
     */
    public function testRegistersServices()
    {
        $container = new ContainerBuilder();

        putenv('PATH=' . (dirname(__DIR__) . '/fixtures'));

        $extension = new PdfLatexExtension();
        $extension->load([], $container);

        $this->assertTrue($container->has('cyberspectrum.pdflatex.executor_factory'));
        $this->assertTrue($container->has('cyberspectrum.pdflatex.processor'));
        $this->assertTrue($container->has('cyberspectrum.pdflatex.twig.extension'));
        $this->assertTrue($container->has('cyberspectrum.pdflatex.twig.file_extension_escaping_strategy'));
    }

    /**
     * Test that the container can be compiled.
     *
     * @return void
     */
    public function testContainerCanBeCompiled()
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.cache_dir', '/does/not/exist');
        putenv('PATH=' . (dirname(__DIR__) . '/fixtures'));

        $extension = new PdfLatexExtension();
        $extension->load([], $container);

        $container->compile();

        $this->assertFalse($container->has('cyberspectrum.pdflatex.executor_factory'));
        $this->assertTrue($container->has('cyberspectrum.pdflatex.processor'));
        $this->assertFalse($container->has('cyberspectrum.pdflatex.twig.extension'));
        $this->assertFalse($container->has('cyberspectrum.pdflatex.twig.file_extension_escaping_strategy'));
    }
}
