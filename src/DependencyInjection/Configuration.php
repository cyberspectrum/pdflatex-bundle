<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Adds the Contao configuration structure.
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('cs_pdflatex');
        $rootNode    = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('cache_dir')
                    ->defaultValue('%kernel.cache_dir%/pdflatex')
                ->end()
                ->scalarNode('pdflatex_binary')
                    ->defaultValue(null)
                ->end()
            ->end();

        return $treeBuilder;
    }
}
