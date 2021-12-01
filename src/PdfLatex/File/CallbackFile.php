<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\PdfLatex\File;

use RuntimeException;

/**
 * This implements a physical file on local disk.
 *
 * @psalm-type TCallbackFileCallback=callable(string, string): void
 */
class CallbackFile implements FileInterface
{
    /**
     * The callable to call when saving the contents.
     *
     * It must accept the destination directory as first argument.
     *
     * @var TCallbackFileCallback
     */
    private $callback;

    /** The name. */
    private string $name;

    /** The subdirectory. */
    private string $directory;

    /**
     * Create a new instance.
     *
     * @param TCallbackFileCallback $callback The callback to invoke.
     * @param string $name                    The file name.
     * @param string $directory               Optional subdirectory to create.
     */
    public function __construct(callable $callback, string $name, string $directory = '')
    {
        $this->callback  = $callback;
        $this->name      = $name;
        $this->directory = $directory;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }

    public function saveTo(string $directory): void
    {
        if ($this->directory) {
            $directory .= DIRECTORY_SEPARATOR . $this->directory;
        }
        call_user_func_array($this->callback, [$directory, $this->name]);
        if (!is_readable($absolutePath = $directory . DIRECTORY_SEPARATOR . $this->name)) {
            throw new RuntimeException('Could not save to ' . $absolutePath);
        }
    }
}
