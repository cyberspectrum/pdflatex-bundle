<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\PdfLatex\File;

/**
 * This implements a physical file on local disk.
 *
 * @psalm-type TCallbackFileCallback=callable(string, string, string): void
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

    /** The sub directory. */
    private string $directory;

    /**
     * Create a new instance.
     *
     * @param TCallbackFileCallback $callback
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
        call_user_func_array($this->callback, [$directory, $this->name, $this->directory]);
    }
}
