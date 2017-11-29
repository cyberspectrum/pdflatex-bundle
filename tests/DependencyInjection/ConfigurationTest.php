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
    /**
     * Test that the bundle can be instantiated.
     *
     * @return void
     */
    public function testCanBeInstantiated()
    {
        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\DependencyInjection\Configuration',
            new Configuration()
        );
    }

    /**
     * Test that the configuration creates a tree builder.
     *
     * @return void
     */
    public function testTreeBuilderIsReturned()
    {
        $config = new Configuration();
        $this->assertInstanceOf(
            TreeBuilder::class,
            $config->getConfigTreeBuilder()
        );
    }
}
