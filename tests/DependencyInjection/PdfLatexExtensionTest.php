<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\Test\DependencyInjection;

use CyberSpectrum\PdfLatexBundle\DependencyInjection\PdfLatexExtension;
use CyberSpectrum\PdfLatexBundle\PdfLatex\ExecutorFactory;
use CyberSpectrum\PdfLatexBundle\PdfLatex\JobProcessor;
use CyberSpectrum\PdfLatexBundle\Twig\Extension;
use CyberSpectrum\PdfLatexBundle\Twig\FileExtensionEscapingStrategy;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension as SymfonyExtension;

use function dirname;
use function getenv;
use function putenv;

/**
 * This tests the Configuration class.
 *
 * @covers \CyberSpectrum\PdfLatexBundle\DependencyInjection\PdfLatexExtension
 */
class PdfLatexExtensionTest extends TestCase
{
    /** Keep $PATH environment. */
    private static string $path;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$path = getenv('PATH');
    }

    public static function tearDownAfterClass(): void
    {
        putenv('PATH=' . self::$path);
        parent::tearDownAfterClass();
    }

    /** Test that the bundle can be instantiated. */
    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\DependencyInjection\PdfLatexExtension',
            $extension = new PdfLatexExtension()
        );
        $this->assertInstanceOf(SymfonyExtension::class, $extension);
    }

    /** Test that the extension uses path override. */
    public function testLoadReturnsOverriddenPathToBinary(): void
    {
        $container = $this
            ->getMockBuilder(ContainerBuilder::class)
            ->onlyMethods(['setParameter'])
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

    /** Test that the extension uses search path by default. */
    public function testLoadReturnsBinaryFromSearchPath(): void
    {
        $container = $this
            ->getMockBuilder(ContainerBuilder::class)
            ->onlyMethods(['setParameter'])
            ->getMock();

        putenv('PATH=' . ($dir = dirname(__DIR__) . '/fixtures'));

        $container
            ->expects($this->once())
            ->method('setParameter')
            ->with('cyberspectrum.pdflatex.binary', $dir . '/pdflatex');

        $extension = new PdfLatexExtension();
        $extension->load([], $container);
    }

    /** Test that the extension throws an exception when no pdflatex is found. */
    public function testLoadThrowsExceptionWhenNoBinaryFound(): void
    {
        $container = $this
            ->getMockBuilder(ContainerBuilder::class)
            ->onlyMethods(['setParameter'])
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

        $this->assertTrue($container->has(ExecutorFactory::class));
        $this->assertTrue($container->has(JobProcessor::class));
        $this->assertTrue($container->has(Extension::class));
        $this->assertTrue($container->has(FileExtensionEscapingStrategy::class));
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

        $this->assertFalse($container->has(ExecutorFactory::class));
        $this->assertTrue($container->has(JobProcessor::class));
        $this->assertFalse($container->has(Extension::class));
        $this->assertFalse($container->has(FileExtensionEscapingStrategy::class));
    }
}
