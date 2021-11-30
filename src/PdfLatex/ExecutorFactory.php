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

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\PdfLatex;

/**
 * This creates executors.
 */
class ExecutorFactory
{
    /**
     * The pdf latex binary.
     *
     * @var string
     */
    private $latexBinary;

    /**
     * Create a new instance.
     *
     * @param string $latexBinary The path to pdflatex.
     */
    public function __construct(string $latexBinary)
    {
        $this->latexBinary = $latexBinary;
    }

    /**
     * Create a pdflatex executor.
     *
     * @param string $directory    The working directory.
     * @param string $texFile      The tex file to process.
     * @param array  $includePaths The include paths.
     *
     * @return Executor
     */
    public function createExecutor(string $directory, string $texFile, array $includePaths = []): Executor
    {
        return new Executor($this->latexBinary, $directory, $texFile, $includePaths);
    }
}
