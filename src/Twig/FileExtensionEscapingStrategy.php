<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\Twig;

use InvalidArgumentException;

use function call_user_func;
use function in_array;
use function is_string;
use function pathinfo;
use function substr;

/**
 * This class injects a tex escaping strategy.
 *
 * @psalm-type TCallback=callable(string): (false|string)
 */
class FileExtensionEscapingStrategy
{
    /**
     * The default strategy to use.
     *
     * @var TCallback
     */
    private $defaultStrategy;

    /**
     * Create a new instance.
     *
     * @param string|TCallback|false $defaultStrategy The default strategy to use when not a .tex file.
     */
    public function __construct($defaultStrategy)
    {
        if (!is_callable($defaultStrategy)) {
            /** @psalm-suppress DocblockTypeContradiction */
            if (!(is_string($defaultStrategy) || false === $defaultStrategy)) {
                throw new InvalidArgumentException('Default strategy must be callable, string or false');
            }

            $this->defaultStrategy = /** @return false|string*/ static function () use ($defaultStrategy) {
                return $defaultStrategy;
            };

            return;
        }

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

        return call_user_func($this->defaultStrategy, $realName);
    }
}
