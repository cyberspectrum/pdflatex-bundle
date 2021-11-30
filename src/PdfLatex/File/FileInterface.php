<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\PdfLatex\File;

use RuntimeException;

/**
 * This interface describes a PdfLatexFile.
 *
 * The files may be anything from .tex documents over .sty files to images.
 */
interface FileInterface
{
    /** Return the file name (i.e. "some-file.tex"). */
    public function getName(): string;

    /** Retrieve the relative directory (i.e. "images"). */
    public function getDirectory(): string;

    /**
     * Save the contents to the passed directory.
     *
     * The file must save itself to the passed directory (including creation of any sub directories).
     *
     * @param string $directory The directory to save to.
     *
     * @throws RuntimeException When anything goes wrong.
     */
    public function saveTo(string $directory): void;
}
