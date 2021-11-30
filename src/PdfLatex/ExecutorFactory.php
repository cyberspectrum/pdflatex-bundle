<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\PdfLatex;

/**
 * This creates executors.
 */
class ExecutorFactory
{
    /** The pdf latex binary. */
    private string $latexBinary;

    /** @param string $latexBinary The path to pdflatex. */
    public function __construct(string $latexBinary)
    {
        $this->latexBinary = $latexBinary;
    }

    /**
     * Create a pdflatex executor.
     *
     * @param string       $directory    The working directory.
     * @param string       $texFile      The tex file to process.
     * @param list<string> $includePaths The include paths.
     *
     * @return Executor
     */
    public function createExecutor(string $directory, string $texFile, array $includePaths = []): Executor
    {
        return new Executor($this->latexBinary, $directory, $texFile, $includePaths);
    }
}
