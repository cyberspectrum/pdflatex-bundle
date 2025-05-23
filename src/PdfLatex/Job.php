<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\PdfLatex;

use CyberSpectrum\PdfLatexBundle\PdfLatex\File\FileInterface;
use InvalidArgumentException;

use function is_dir;
use function realpath;
use function substr;
use function uniqid;

/**
 * This class describes a pdflatex job.
 */
final class Job
{
    /** The tex file to render. */
    private readonly FileInterface $texFile;

    /** The name of the job. */
    private readonly string $jobName;

    /**
     * The assets to add.
     *
     * @var list<FileInterface>
     */
    private array $assets;

    /**
     * Additional include paths to be passed as TEXINPUTS environment variable.
     *
     * @var list<string>
     */
    private array $includePaths;

    /**
     * Create a new instance.
     *
     * @param FileInterface $texFile The TeX file of this job.
     * @param string       $jobName The job name (if empty a random name will get used).
     *
     * @throws InvalidArgumentException When the passed file is not a .tex file.
     */
    public function __construct(FileInterface $texFile, string $jobName = '')
    {
        if (!str_ends_with($name = $texFile->getName(), '.tex')) {
            throw new InvalidArgumentException('File ' . $name . ' does not have file extension ".tex"');
        }
        if ('' === $jobName) {
            $jobName = uniqid('job-');
        }

        $this->texFile = $texFile;
        $this->jobName = $jobName;
        $this->assets = [];
        $this->includePaths = [];
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

    /** Retrieve jobName. */
    public function getJobName(): string
    {
        return $this->jobName;
    }

    /**
     * Add an asset.
     *
     * @param FileInterface $asset The asset to add.
     */
    public function addAsset(FileInterface $asset): self
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
     * Add include path.
     *
     * @param string $path The path to add.
     *
     * @throws InvalidArgumentException When the path does not exist.
     */
    public function addIncludePath(string $path): self
    {
        $realPath = realpath($path);
        if ((false === $realPath) || !is_dir($realPath)) {
            throw new InvalidArgumentException('Not a directory: ' . $path);
        }
        // Allow recursive inclusion.
        if (str_ends_with($path, '//')) {
            $realPath .= '//';
        }
        $this->includePaths[] = $realPath;

        return $this;
    }

    /**
     * Retrieve includePaths.
     *
     * @return list<string>
     */
    public function getIncludePaths(): array
    {
        return $this->includePaths;
    }
}
