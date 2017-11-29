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

namespace CyberSpectrum\PdfLatexBundle\PdfLatex\File;

/**
 * This implements a physical file on local disk.
 */
class PhysicalFile extends AbstractStreamedFile
{
    /**
     * The file path.
     *
     * @var string
     */
    private $path;

    /**
     * The sub directory.
     *
     * @var string
     */
    private $directory;

    /**
     * Create a new instance.
     *
     * @param string   $path      The file name.
     * @param string   $directory The optional sub directory.
     *
     * @throws \InvalidArgumentException When an invalid path has been passed.
     */
    public function __construct(string $path, string $directory = '')
    {
        if (false === \is_file($path)) {
            throw new \InvalidArgumentException(\sprintf('File %s does not exist.', $path));
        }

        $this->path      = $path;
        $this->directory = $directory;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return basename($this->path);
    }

    /**
     * {@inheritDoc}
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \RuntimeException When anything goes wrong.
     */
    public function saveTo(string $directory)
    {
        $source = \fopen($this->path, 'rb');
        try {
            $this->save($source, $directory);
        } finally {
            \fclose($source);
        }
    }
}
