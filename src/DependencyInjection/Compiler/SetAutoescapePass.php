<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This replaces the twig escaping strategy to try tex first.
 */
class SetAutoescapePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        // No twig? can not inject.
        if (!$container->hasDefinition('twig')) {
            return;
        }

        $twig   = $container->getDefinition('twig');
        $config = $twig->getArgument(1);
        if (!is_string($config['autoescape']) || 'name' === $config['autoescape']) {
            if ('name' === $config['autoescape']) {
                $config['autoescape'] = ['\Twig\FileExtensionEscapingStrategy', 'guess'];
            }
            $escaper = $container->getDefinition('cyberspectrum.pdflatex.twig.file_extension_escaping_strategy');
            $escaper->setArgument(0, $config['autoescape']);

            $config['autoescape'] =
                [new Reference('cyberspectrum.pdflatex.twig.file_extension_escaping_strategy'), 'guess'];
            $twig->replaceArgument(1, $config);
        }
    }
}
