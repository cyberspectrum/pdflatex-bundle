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
 * This implements a file from a stream.
 */
class StreamedFile extends AbstractStreamedFile
{
    /**
     * The stream.
     *
     * @var resource
     */
    private $stream;

    /**
     * The file name.
     *
     * @var string
     */
    private $name;

    /**
     * The sub directory.
     *
     * @var string
     */
    private $directory;

    /**
     * Create a new instance.
     *
     * @param resource $stream    The stream to use.
     * @param string   $name      The file name.
     * @param string   $directory The optional sub directory.
     *
     * @throws \InvalidArgumentException When no resource has been passed.
     */
    public function __construct($stream, string $name, string $directory = '')
    {
        if (false === \is_resource($stream)) {
            throw new \InvalidArgumentException(
                \sprintf('Argument must be a valid resource type. %s given.', gettype($stream))
            );
        }

        $this->stream    = $stream;
        $this->name      = $name;
        $this->directory = $directory;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException When anything goes wrong.
     */
    public function saveTo(string $directory)
    {
        $this->save($this->stream, $directory);
    }
}
