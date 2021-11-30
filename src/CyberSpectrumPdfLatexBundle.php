<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle;

use CyberSpectrum\PdfLatexBundle\DependencyInjection\Compiler\AddEscaperPass;
use CyberSpectrum\PdfLatexBundle\DependencyInjection\Compiler\SetAutoescapePass;
use CyberSpectrum\PdfLatexBundle\DependencyInjection\PdfLatexExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CyberSpectrumPdfLatexBundle extends Bundle
{
    public function getContainerExtension(): PdfLatexExtension
    {
        return new PdfLatexExtension();
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new SetAutoescapePass());
        $container->addCompilerPass(new AddEscaperPass());
    }
}
