<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\Twig;

use CyberSpectrum\PdfLatexBundle\Helper\TextUtils;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Extension\EscaperExtension;
use Twig\TwigFilter;

/**
 * This class provides certain twig extensions.
 */
class Extension extends AbstractExtension
{
    /** The text utils to use. */
    private TextUtils $utils;

    /**
     * Create a new instance.
     *
     * @param TextUtils|null $utils The text utils to use.
     */
    public function __construct(?TextUtils $utils = null)
    {
        $this->utils = $utils ?: new TextUtils();
    }

    /**
     * Add the escaper to the environment.
     *
     * @param Environment $environment The twig environment.
     */
    public function addEscaperTo(Environment $environment): void
    {
        /** @var EscaperExtension $extension */
        $extension = $environment->getExtension(EscaperExtension::class);
        $extension->setEscaper('tex', [$this, 'escape']);
    }

    /**
     * Escape the passed input.
     *
     * @param Environment $twig    The twig environment.
     * @param string|null $string  The string to escape.
     * @param string|null $charset The charset.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) - The interface is dictated by twig.
     */
    public function escape(Environment $twig, string $string = null, string $charset = null)
    {
        if (empty($string)) {
            return '';
        }
        return $this->texifyAll($string);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
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
     */
    public function texify(string $text): string
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
     */
    public function texifyAll(string $text): string
    {
        if (empty($text)) {
            return $text;
        }

        return $this->utils->parseText($text, true);
    }
}
