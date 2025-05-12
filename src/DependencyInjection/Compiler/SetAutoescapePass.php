<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\DependencyInjection\Compiler;

use CyberSpectrum\PdfLatexBundle\Twig\FileExtensionEscapingStrategy;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Twig\FileExtensionEscapingStrategy as TwigFileExtensionEscapingStrategy;

/**
 * This replaces the twig escaping strategy to try tex first.
 */
final readonly class SetAutoescapePass implements CompilerPassInterface
{
    #[\Override]
    public function process(ContainerBuilder $container): void
    {
        // No twig? can not inject.
        if (!$container->hasDefinition('twig')) {
            return;
        }

        $twig = $container->getDefinition('twig');
        /** @var array{autoescape: mixed} $config */
        $config = $twig->getArgument(1);
        if (!is_string($config['autoescape']) || 'name' === $config['autoescape']) {
            if ('name' === $config['autoescape']) {
                $config['autoescape'] = [TwigFileExtensionEscapingStrategy::class, 'guess'];
            }
            $escaper = $container->getDefinition(FileExtensionEscapingStrategy::class);
            $escaper->setArgument(0, $config['autoescape']);

            $config['autoescape'] =
                [new Reference(FileExtensionEscapingStrategy::class), 'guess'];
            $twig->replaceArgument(1, $config);
        }
    }
}
