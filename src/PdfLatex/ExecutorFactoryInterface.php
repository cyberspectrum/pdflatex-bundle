<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\PdfLatex;

interface ExecutorFactoryInterface
{
    /**
     * Create a pdflatex executor.
     *
     * @param string       $directory    The working directory.
     * @param string       $texFile      The tex file to process.
     * @param list<string> $includePaths The include paths.
     */
    public function createExecutor(string $directory, string $texFile, array $includePaths = []): ExecutorInterface;
}
