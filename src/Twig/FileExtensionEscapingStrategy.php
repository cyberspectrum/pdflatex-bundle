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

namespace CyberSpectrum\PdfLatexBundle\Twig;

/**
 * This class injects a tex escaping strategy.
 */
class FileExtensionEscapingStrategy
{
    /**
     * The default strategy to use.
     *
     * @var string|callable|false
     */
    private $defaultStrategy;

    /**
     * Create a new instance.
     *
     * @param string|callable|false $defaultStrategy The default strategy to use when not a .tex file.
     */
    public function __construct($defaultStrategy)
    {
        $this->defaultStrategy = $defaultStrategy;
    }

    /**
     * Guess the escaping strategy based upon the passed file name.
     *
     * Much of this code is based upon \Twig_FileExtensionEscapingStrategy::guess().
     *
     * @param string $name The file name to giess the escaping strategy for.
     *
     * @return string|false The escaping strategy name to use or false to disable
     */
    public function guess(string $name)
    {
        // See \Twig_FileExtensionEscapingStrategy::guess
        if (in_array(substr($name, -1), ['/', '\\'])) {
            // return html for directories
            return 'html';
        }

        $realName = $name;
        if ('.twig' === substr($name, -5)) {
            $name = substr($name, 0, -5);
        }

        $extension = pathinfo($name, PATHINFO_EXTENSION);
        if ('tex' === $extension) {
            return 'tex';
        }

        if (false !== $this->defaultStrategy && !is_string($this->defaultStrategy)) {
            return call_user_func($this->defaultStrategy, $realName);
        }

        return $this->defaultStrategy;
    }
}
