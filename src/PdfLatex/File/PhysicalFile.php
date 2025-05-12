<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\PdfLatex\File;

use InvalidArgumentException;
use RuntimeException;

use function basename;
use function fclose;
use function fopen;
use function is_file;
use function sprintf;

/**
 * This implements a physical file on local disk.
 */
final class PhysicalFile extends AbstractStreamedFile
{
    /** The file path. */
    private readonly string $path;

    /** The subdirectory. */
    private readonly string $directory;

    /**
     * Create a new instance.
     *
     * @param string $path      The file name.
     * @param string $directory The optional subdirectory.
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

    #[\Override]
    public function getName(): string
    {
        return basename($this->path);
    }

    #[\Override]
    public function getDirectory(): string
    {
        return $this->directory;
    }

    #[\Override]
    public function saveTo(string $directory): void
    {
        $source = fopen($this->path, 'rb');
        if (false === $source) {
            throw new RuntimeException('Could not open ' . $this->path);
        }

        try {
            $this->save($source, $directory);
        } finally {
            fclose($source);
        }
    }
}
