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

namespace CyberSpectrum\PdfLatexBundle\PdfLatex;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * This class processes jobs.
 */
class JobProcessor
{
    /**
     * The executor factory.
     *
     * @var ExecutorFactory
     */
    private $executorFactory;

    /**
     * The temporary base directory.
     *
     * @var string
     */
    private $tempDirectory;

    /**
     * Create a new instance.
     *
     * @param ExecutorFactory $executorFactory The executor factory.
     * @param string          $tempDirectory   The temporary directory.
     */
    public function __construct(ExecutorFactory $executorFactory, string $tempDirectory)
    {
        $this->executorFactory = $executorFactory;
        $this->tempDirectory   = $tempDirectory;
    }

    /**
     * Process the passed TeX file and return the path to the generated .pdf.
     *
     * @param Job $latexJob The job to process.
     *
     * @return string
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
     *
     * @return BinaryFileResponse
     */
    public function createPdfResponse(Job $latexJob)
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
