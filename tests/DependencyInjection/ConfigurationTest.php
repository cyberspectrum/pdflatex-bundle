<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\Test\DependencyInjection;

use CyberSpectrum\PdfLatexBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * This tests the Configuration class.
 *
 * @covers \CyberSpectrum\PdfLatexBundle\DependencyInjection\Configuration
 */
class ConfigurationTest extends TestCase
{
    /** Test that the bundle can be instantiated. */
    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\DependencyInjection\Configuration',
            new Configuration()
        );
    }

    /** Test that the configuration creates a tree builder. */
    public function testTreeBuilderIsReturned(): void
    {
        $config = new Configuration();
        $this->assertInstanceOf(
            TreeBuilder::class,
            $config->getConfigTreeBuilder()
        );
    }
}
