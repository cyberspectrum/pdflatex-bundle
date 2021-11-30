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

namespace CyberSpectrum\PdfLatexBundle\PdfLatex\File;

/**
 * This interface describes a PdfLatexFile.
 *
 * The files may be anything from .tex documents over .sty files to images.
 */
interface FileInterface
{
    /**
     * Return the file name (i.e. "some-file.tex").
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Retrieve the relative directory (i.e. "images").
     *
     * @return string
     */
    public function getDirectory(): string;

    /**
     * Save the contents to the passed directory.
     *
     * The file must save itself to the passed directory (including creation of any sub directories).
     *
     * @param string $directory The directory to save to.
     *
     * @throws \RuntimeException When anything goes wrong.
     * @return void
     */
    public function saveTo(string $directory);
}
