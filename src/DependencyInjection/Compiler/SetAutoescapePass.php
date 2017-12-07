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

namespace CyberSpectrum\PdfLatexBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This replaces the twig escaping strategy to try tex first.
 */
class SetAutoescapePass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
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
