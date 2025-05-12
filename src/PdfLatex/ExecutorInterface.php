<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\PdfLatex;

interface ExecutorInterface
{
    /**
     * Run latex on the passed file and return the path to the PDF.
     *
     * @param null|string $outputDirectory The optional output directory, if different from source directory.
     */
    public function run(?string $outputDirectory = null): string;
}
