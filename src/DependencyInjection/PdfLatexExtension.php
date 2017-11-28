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

namespace CyberSpectrum\PdfLatexBundle\DependencyInjection;

use RuntimeException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Process\ExecutableFinder;

/**
 * This is the extension.
 */
class PdfLatexExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        $container->setParameter(
            'cyberspectrum.pdflatex.binary',
            $config['pdflatex_binary'] ?: $this->getDefaultBinary()
        );
    }

    /**
     * Find the default pdflatex binary.
     *
     * @return string
     *
     * @throws RuntimeException When the processor could not be found.
     */
    private function getDefaultBinary(): string
    {
        $finder = new ExecutableFinder();

        if (null === ($binary = $finder->find('pdflatex'))) {
            throw new RuntimeException('Could not find a pdflatex binary.');
        }

        return $binary;
    }
}
