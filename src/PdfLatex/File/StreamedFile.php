<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\PdfLatex\File;

use InvalidArgumentException;

use function is_resource;
use function sprintf;

/**
 * This implements a file from a stream.
 */
final class StreamedFile extends AbstractStreamedFile
{
    /**
     * The stream.
     *
     * @var resource
     */
    private $stream;

    /** The file name. */
    private readonly string $name;

    /** The subdirectory. */
    private readonly string $directory;

    /**
     * Create a new instance.
     *
     * @param resource $stream    The stream to use.
     * @param string   $name      The file name.
     * @param string   $directory The optional subdirectory.
     *
     * @throws InvalidArgumentException When no resource has been passed.
     */
    public function __construct($stream, string $name, string $directory = '')
    {
        /** @psalm-suppress DocblockTypeContradiction */
        if (!is_resource($stream)) {
            throw new InvalidArgumentException(
                sprintf('Argument must be a valid resource type. %s given.', gettype($stream))
            );
        }

        $this->stream    = $stream;
        $this->name      = $name;
        $this->directory = $directory;
    }

    #[\Override]
    public function getName(): string
    {
        return $this->name;
    }

    #[\Override]
    public function getDirectory(): string
    {
        return $this->directory;
    }

    #[\Override]
    public function saveTo(string $directory): void
    {
        $this->save($this->stream, $directory);
    }
}
