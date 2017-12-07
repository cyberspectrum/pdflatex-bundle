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

namespace CyberSpectrum\PdfLatexBundle\Twig;

use CyberSpectrum\PdfLatexBundle\Helper\TextUtils;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

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
     *
     * @param TextUtils|null $utils The text utils to use.
     */
    public function __construct(TextUtils $utils = null)
    {
        $this->utils = $utils ?: new TextUtils();
    }

    /**
     * Add the escaper to the environment.
     *
     * @param Environment $environment The twig environment.
     *
     * @return void
     */
    public function addEscaperTo(Environment $environment)
    {
        $environment->getExtension('Twig\Extension\CoreExtension')->setEscaper('tex', [$this, 'escape']);
        /** @var \Twig_Extension_Escaper $escaper */
        $escaper    = $environment->getExtension('Twig\Extension\EscaperExtension');
        $reflection = new \ReflectionProperty($escaper, 'defaultStrategy');
        $reflection->setAccessible(true);
    }

    /**
     * Escape the passed input.
     *
     * @param Environment $twig    The twig environment.
     * @param string      $string  The string to escape.
     * @param string      $charset The charset.
     *
     * @return string
     *
     * @@SuppressWarnings(PHPMD.UnusedFormalParameter) - The interface is dictated by twig.
     */
    public function escape(Environment $twig, string $string, string $charset)
    {
        return $this->texifyAll($string);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('texify', [$this, 'texify'], ['is_safe' => ['tex']]),
            new TwigFilter('texify_all', [$this, 'texifyAll'], ['is_safe' => ['tex']]),
        ];
    }

    /**
     * Escape LaTeX chars.
     *
     * @param string $text The text to escape.
     *
     * @return string
     */
    public function texify(string $text)
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
    public function texifyAll(string $text)
    {
        if (empty($text)) {
            return $text;
        }

        return $this->utils->parseText($text, true);
    }
}
