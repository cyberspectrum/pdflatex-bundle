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
class CallbackFile implements FileInterface
{
    /**
     * The callable to call when saving the contents.
     *
     * It must accept the destination directory as first argument.
     *
     * @var callable
     */
    private $callback;

    /**
     * The name.
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
     * @param callable $callback
     * @param string   $name
     * @param string   $directory
     */
    public function __construct(callable $callback, string $name, string $directory = '')
    {
        $this->callback  = $callback;
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
     */
    public function saveTo(string $directory)
    {
        call_user_func_array($this->callback, [$directory, $this->name, $this->directory]);
    }
}
