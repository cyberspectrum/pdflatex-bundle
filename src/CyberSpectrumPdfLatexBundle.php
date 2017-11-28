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

declare (strict_types = 1);

namespace CyberSpectrum\PdfLatexBundle;

use CyberSpectrum\PdfLatexBundle\DependencyInjection\PdfLatexExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * This is the bundle.
 */
class CyberSpectrumPdfLatexBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new PdfLatexExtension();
    }
}
