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

use CyberSpectrum\PdfLatexBundle\PdfLatex\File\FileInterface;

/**
 * This class describes a pdflatex job.
 */
class Job
{
    /**
     * The tex file to render.
     *
     * @var FileInterface
     */
    private $texFile = '';

    /**
     * The name of the job.
     *
     * @var string
     */
    private $jobName;

    /**
     * The assets to add.
     *
     * @var FileInterface[]
     */
    private $assets = [];

    /**
     * Additional include paths to be passed as TEXINPUTS environment variable.
     *
     * @var string[]
     */
    private $includePaths = [];

    /**
     * Create a new instance.
     *
     * @param FileInterface $texFile The TeX file of this job.
     * @param string        $jobName The job name (if empty a random name will get used).
     *
     * @throws \InvalidArgumentException When the passed file is not a .tex file.
     */
    public function __construct(FileInterface $texFile, string $jobName = null)
    {
        if ('.tex' !== substr($name = $texFile->getName(), -4)) {
            throw new \InvalidArgumentException('File ' . $name . ' does not have file extension ".tex"');
        }
        if (empty($jobName)) {
            $jobName = uniqid('job-');
        }

        $this->texFile = $texFile;
        $this->jobName = $jobName;
    }

    /**
     * Retrieve texFile.
     *
     * @return FileInterface
     */
    public function getTexFile(): FileInterface
    {
        return $this->texFile;
    }

    /**
     * Retrieve jobName.
     *
     * @return string
     */
    public function getJobName()
    {
        return $this->jobName;
    }

    /**
     * Add an include path.
     *
     * @param FileInterface $asset The asset to add.
     *
     * @return Job
     */
    public function addAsset(FileInterface $asset): Job
    {
        $this->assets[] = $asset;

        return $this;
    }

    /**
     * Retrieve includePaths.
     *
     * @return FileInterface[]
     */
    public function getAssets(): array
    {
        return $this->assets;
    }

    /**
     * Add an include path.
     *
     * @param string $path The path to add.
     *
     * @return Job
     *
     * @throws \InvalidArgumentException When the path does not exist.
     */
    public function addIncludePath(string $path): Job
    {
        $realPath = realpath($path);
        if ((null === $realPath) || !is_dir($realPath)) {
            throw new \InvalidArgumentException('Not a directory: ' . $path);
        }
        $this->includePaths[] = $realPath;

        return $this;
    }

    /**
     * Retrieve includePaths.
     *
     * @return string[]
     */
    public function getIncludePaths(): array
    {
        return $this->includePaths;
    }
}
