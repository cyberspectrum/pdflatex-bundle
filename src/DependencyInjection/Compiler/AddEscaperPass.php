<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\DependencyInjection\Compiler;

use CyberSpectrum\PdfLatexBundle\Twig\Extension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This adds the tex escaper to the escapers.
 */
final readonly class AddEscaperPass implements CompilerPassInterface
{
    #[\Override]
    public function process(ContainerBuilder $container): void
    {
        // No twig? can not inject.
        if (!$container->hasDefinition('twig')) {
            return;
        }

        $extension = $container->getDefinition(Extension::class);
        $extension->addMethodCall('addEscaperTo', [new Reference('twig')]);
    }
}
