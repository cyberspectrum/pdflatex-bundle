<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\PdfLatex\File;

use InvalidArgumentException;

use function basename;
use function fclose;
use function fopen;
use function is_file;
use function sprintf;

/**
 * This implements a physical file on local disk.
 */
class PhysicalFile extends AbstractStreamedFile
{
    /** The file path. */
    private string $path;

    /** The sub directory. */
    private string $directory;

    /**
     * Create a new instance.
     *
     * @param string $path      The file name.
     * @param string $directory The optional sub directory.
     *
     * @throws InvalidArgumentException When an invalid path has been passed.
     */
    public function __construct(string $path, string $directory = '')
    {
        if (false === is_file($path)) {
            throw new InvalidArgumentException(sprintf('File %s does not exist.', $path));
        }

        $this->path      = $path;
        $this->directory = $directory;
    }

    public function getName(): string
    {
        return basename($this->path);
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }

    public function saveTo(string $directory): void
    {
        $source = fopen($this->path, 'rb');

        try {
            $this->save($source, $directory);
        } finally {
            fclose($source);
        }
    }
}
