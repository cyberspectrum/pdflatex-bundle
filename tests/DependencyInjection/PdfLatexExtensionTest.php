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
use Symfony\Component\DependencyInjection\Definition;
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
        self::assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\DependencyInjection\PdfLatexExtension',
            $extension = new PdfLatexExtension()
        );
        self::assertInstanceOf(SymfonyExtension::class, $extension);
    }

    /** Test that the extension uses path override. */
    public function testLoadReturnsOverriddenPaths(): void
    {
        $container = $this
            ->getMockBuilder(ContainerBuilder::class)
            ->onlyMethods(['getDefinition'])
            ->getMock();

        $factory = $this->getMockBuilder(Definition::class)->onlyMethods(['setArgument'])->getMock();
        $factory->expects(self::once())->method('setArgument')->with('$latexBinary', '/bin/false');
        $processor = $this->getMockBuilder(Definition::class)->onlyMethods(['setArgument'])->getMock();
        $processor->expects(self::once())->method('setArgument')->with('$tempDirectory', '/tmp/path');

        $container
            ->expects(self::exactly(2))
            ->method('getDefinition')
            ->will(self::returnValueMap([
                [ExecutorFactory::class, $factory],
                [JobProcessor::class, $processor],
            ]));

        $extension = new PdfLatexExtension();
        $extension->load([['pdflatex_binary' => '/bin/false', 'cache_dir' => '/tmp/path']], $container);
    }

    /** Test that the extension uses search path by default. */
    public function testLoadReturnsBinaryFromSearchPath(): void
    {
        $container = $this
            ->getMockBuilder(ContainerBuilder::class)
            ->onlyMethods(['getDefinition'])
            ->getMock();

        putenv('PATH=' . ($dir = dirname(__DIR__) . '/fixtures'));

        $factory = $this->getMockBuilder(Definition::class)->onlyMethods(['setArgument'])->getMock();
        $factory
            ->expects(self::once())
            ->method('setArgument')
            ->with('$latexBinary', $dir . '/pdflatex');
        $processor = $this->getMockBuilder(Definition::class)->onlyMethods(['setArgument'])->getMock();
        $processor
            ->expects(self::once())
            ->method('setArgument')
            ->with('$tempDirectory', '%kernel.cache_dir%/pdflatex');

        $container
            ->expects(self::exactly(2))
            ->method('getDefinition')
            ->will(self::returnValueMap([
                [ExecutorFactory::class, $factory],
                [JobProcessor::class, $processor],
            ]));

        $extension = new PdfLatexExtension();
        $extension->load([], $container);
    }

    /** Test that the extension throws an exception when no pdflatex is found. */
    public function testLoadThrowsExceptionWhenNoBinaryFound(): void
    {
        $container = $this
            ->getMockBuilder(ContainerBuilder::class)
            ->onlyMethods(['getDefinition'])
            ->getMock();

        putenv('PATH=');

        $container
            ->expects(self::never())
            ->method('getDefinition');

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Could not find a pdflatex binary.');

        $extension = new PdfLatexExtension();
        $extension->load([], $container);
    }

    /** Test that the services are being registered. */
    public function testRegistersServices(): void
    {
        $container = new ContainerBuilder();

        putenv('PATH=' . (dirname(__DIR__) . '/fixtures'));

        $extension = new PdfLatexExtension();
        $extension->load([], $container);

        self::assertTrue($container->has(ExecutorFactory::class));
        self::assertTrue($container->has(JobProcessor::class));
        self::assertTrue($container->has(Extension::class));
        self::assertTrue($container->has(FileExtensionEscapingStrategy::class));
    }

    /** Test that the container can be compiled. */
    public function testContainerCanBeCompiled(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.cache_dir', '/does/not/exist');
        putenv('PATH=' . (dirname(__DIR__) . '/fixtures'));

        $extension = new PdfLatexExtension();
        $extension->load([], $container);

        $container->compile();

        self::assertFalse($container->has(ExecutorFactory::class));
        self::assertTrue($container->has(JobProcessor::class));
        self::assertFalse($container->has(Extension::class));
        self::assertFalse($container->has(FileExtensionEscapingStrategy::class));
    }
}
