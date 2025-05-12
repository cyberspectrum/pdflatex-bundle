<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\Twig;

use CyberSpectrum\PdfLatexBundle\Helper\TextUtils;
use CyberSpectrum\PdfLatexBundle\Helper\TextUtilsInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Runtime\EscaperRuntime;
use Twig\TwigFilter;

/**
 * This class provides certain twig extensions.
 */
final class Extension extends AbstractExtension
{
    /** The text utils to use. */
    private TextUtilsInterface $utils;

    /**
     * Create a new instance.
     *
     * @param TextUtilsInterface|null $utils The text utils to use.
     */
    public function __construct(?TextUtilsInterface $utils = null)
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
        $runtime = $environment->getRuntime(EscaperRuntime::class);

        $runtime->setEscaper('tex', [$this, 'escape']);
    }

    /**
     * Escape the passed input.
     *
     * @param string $string  The string to escape.
     * @param string $charset The charset.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) - The interface is dictated by twig.
     * @psalm-suppress UnusedParam
     * @psalm-suppress PossiblyUnusedReturnValue
     */
    public function escape(string $string, string $charset): string
    {
        if ('' === $string) {
            return '';
        }

        return $this->texifyAll($string);
    }

    #[\Override]
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
