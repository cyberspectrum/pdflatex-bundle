<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\Helper;

/**
 * This class pre-processes text for usage in TeX documents.
 */
interface TextUtilsInterface
{
    /**
     * Parse the text and replace known special latex characters correctly.
     *
     * @param string $text          The string that needs to be parsed.
     * @param bool   $escapeNewLine If set, newline characters will be replaced by LaTeX entities (default false).
     */
    public function parseText(string $text, bool $escapeNewLine = false): string;
}
