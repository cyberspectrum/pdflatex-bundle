<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\PdfLatex;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * This class processes jobs.
 */
final class JobProcessor
{
    /** The executor factory. */
    private ExecutorFactoryInterface $executorFactory;

    /** The temporary base directory. */
    private string $tempDirectory;

    /**
     * @param ExecutorFactoryInterface $executorFactory The executor factory.
     * @param string                   $tempDirectory   The temporary directory.
     */
    public function __construct(ExecutorFactoryInterface $executorFactory, string $tempDirectory)
    {
        $this->executorFactory = $executorFactory;
        $this->tempDirectory   = $tempDirectory;
    }

    /**
     * Process the passed TeX file and return the path to the generated .pdf.
     *
     * @param Job $latexJob The job to process.
     */
    public function process(Job $latexJob): string
    {
        $tempDir = $this->tempDirectory . DIRECTORY_SEPARATOR . $latexJob->getJobName();

        $latexJob->getTexFile()->saveTo($tempDir);
        foreach ($latexJob->getAssets() as $asset) {
            $asset->saveTo($tempDir);
        }

        $executor = $this->executorFactory->createExecutor(
            $tempDir,
            $latexJob->getTexFile()->getName(),
            $latexJob->getIncludePaths()
        );

        return $executor->run();
    }

    /**
     * Generate a response containing a PDF document.
     *
     * @param Job $latexJob The job to process.
     */
    public function createPdfResponse(Job $latexJob): BinaryFileResponse
    {
        $pdfLocation = $this->process($latexJob);
        $response    = new BinaryFileResponse($pdfLocation);
        $response->headers->set('Content-Type', 'application/pdf;charset=utf-8');
        $response->headers->set(
            'Content-Disposition',
            'attachment;filename="' . basename($latexJob->getTexFile()->getName(), '.tex') . '.pdf"'
        );

        return $response;
    }
}
