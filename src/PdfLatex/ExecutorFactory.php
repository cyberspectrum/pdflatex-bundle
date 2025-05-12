<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\PdfLatex;

/**
 * This creates executors.
 */
final class ExecutorFactory implements ExecutorFactoryInterface
{
    /** The pdf latex binary. */
    private readonly string $latexBinary;

    /** @param string $latexBinary The path to pdflatex. */
    public function __construct(string $latexBinary)
    {
        $this->latexBinary = $latexBinary;
    }

    #[\Override]
    public function createExecutor(string $directory, string $texFile, array $includePaths = []): ExecutorInterface
    {
        return new Executor($this->latexBinary, $directory, $texFile, $includePaths);
    }
}
