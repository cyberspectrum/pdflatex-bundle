<?php

declare(strict_types=1);

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
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $configuration = $this->getConfiguration($configs, $container);
        assert($configuration instanceof Configuration);
        /** @var array{pdflatex_binary: string|null} $config */
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(
            'cyberspectrum.pdflatex.binary',
            $config['pdflatex_binary'] ?? $this->getDefaultBinary()
        );
    }

    /**
     * Find the default pdflatex binary.
     *
     * @throws RuntimeException When the processor could not be found.
     * @return string
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
