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

namespace CyberSpectrum\PdfLatexBundle\Twig;

use CyberSpectrum\PdfLatexBundle\Helper\TextUtils;
use Twig\Extension\AbstractExtension;

/**
 * This class provides certain twig extensions.
 */
class Extension extends AbstractExtension
{
    /**
     * The text utils to use.
     *
     * @var TextUtils
     */
    private $utils;

    /**
     * Create a new instance.
     */
    public function __construct()
    {
        $this->utils = new TextUtils();
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('texify', [$this, 'texify']),
            new \Twig_SimpleFilter('texify_all', [$this, 'texifyAll']),
        ];
    }

    /**
     * Escape LaTeX chars.
     *
     * @param string $text The text to escape.
     *
     * @return string
     */
    public function texify($text)
    {
        if (empty($text)) {
            return $text;
        }
        return $this->utils->parseText($text);
    }

    /**
     * Escape LaTeX chars.
     *
     * @param string $text The text to escape.
     *
     * @return string
     */
    public function texifyAll($text)
    {
        if (empty($text)) {
            return $text;
        }
        return $this->utils->parseText($text, true);
    }
}
