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
declare(strict_types = 1);

namespace CyberSpectrum\PdfLatexBundle\PdfLatex;

use CyberSpectrum\PdfLatexBundle\Exception\LatexFailedException;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Process\Process;

/**
 * This class runs pdflatex on a passed file.
 */
class Executor
{
    /**
     * The path to the pdflatex binary.
     *
     * @var string
     */
    private $binary;

    /**
     * The working directory.
     *
     * @var string
     */
    private $directory;

    /**
     * The tex file to process.
     *
     * @var string
     */
    private $texFile;

    /**
     * The pdf file being generated.
     *
     * @var string
     */
    private $pdfFile;

    /**
     * The list of include paths.
     *
     * @var array
     */
    private $includePaths;

    /**
     * Create a new instance.
     *
     * @param string $binary       The pdflatex binary.
     * @param string $directory    The working directory.
     * @param string $texFile      The tex file name to process (relative to working directory).
     * @param array  $includePaths Additional include paths to be passed as TEXINPUTS environment variable.
     *
     * @throws InvalidArgumentException If the file name is not a .tex file or the input file does not exist.
     */
    public function __construct(string $binary, string $directory, string $texFile, array $includePaths)
    {
        if (!is_executable($binary)) {
            throw new InvalidArgumentException('File ' . $binary . ' is not executable.');
        }

        if ('.tex' !== substr($texFile, -4)) {
            throw new InvalidArgumentException('File ' . $texFile . ' does not have file extension ".tex".');
        }

        if (!is_file($directory . DIRECTORY_SEPARATOR . $texFile)) {
            throw new InvalidArgumentException(
                'File ' . $directory . DIRECTORY_SEPARATOR . $texFile . ' does not exist.'
            );
        }

        $this->directory    = $directory;
        $this->texFile      = $texFile;
        $this->pdfFile      = substr($texFile, 0, -4) . '.pdf';
        $this->binary       = $binary;
        $this->includePaths = $includePaths;
    }

    /**
     * Run latex on the passed file and return the path to the PDF.
     *
     * @param null|string $outputDirectory The optional output directory, if different than source directory.
     *
     * @return string
     */
    public function run(string $outputDirectory = null)
    {
        $compile = true;
        $count   = 0;

        $compileOptions = [
            'halt-on-error'    => '',
            'output-directory' => $outputDirectory ?: $this->directory,
            'interaction'      => 'nonstopmode',
        ];

        $options = [];
        foreach ($compileOptions as $option => $value) {
            $options[] = '-' . $option . (($value) ? '=' . $value : '');
        }

        // Compile until everything is ok or three times is reached.
        while ($compile && $count < 3) {
            ++$count;
            $compile = $this->latexPass($options);
        }

        return $this->directory . DIRECTORY_SEPARATOR . $this->pdfFile;
    }

    /**
     * Run latex over the file and return true if another pass is needed.
     *
     * @param array $options The additional options.
     *
     * @return bool
     *
     * @throws RuntimeException     When the process did not create a pdf file.
     * @throws LatexFailedException When the process exited non zero.
     */
    private function latexPass(array $options): bool
    {
        $process = new Process(
            array_merge([$this->binary], $options, $this->texFile),
            $this->directory
        );
        $process->setTimeout(60);
        $process->run(null, ['TEXINPUTS' => implode(PATH_SEPARATOR, $this->includePaths)]);
        // Check if the pdflatex command completed successfully
        if (!$process->isSuccessful()) {
            throw new LatexFailedException($process);
        }

        if (!is_file($this->directory . DIRECTORY_SEPARATOR . $this->pdfFile)) {
            throw new RuntimeException('pdflatex failed to produce pdf file.');
        }

        return 0 < preg_match_all('/reference|change|no file /uim', $process->getOutput());
    }
}
