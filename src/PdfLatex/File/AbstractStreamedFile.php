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

declare (strict_types = 1);

namespace CyberSpectrum\PdfLatexBundle\PdfLatex\File;

use Symfony\Component\Filesystem\Filesystem;

/**
 * This implements saving a file from a stream.
 */
abstract class AbstractStreamedFile implements FileInterface
{
    /**
     * Save the contents from the source stream to the destination file.
     *
     * @param resource $source      The source stream.
     * @param string   $destination The destination directory.
     *
     * @return void
     *
     * @throws \RuntimeException When anything goes wrong.
     */
    protected function save($source, string $destination)
    {
        if (!empty($directory = $this->getDirectory())) {
            $destination .= DIRECTORY_SEPARATOR . $directory;
            try {
                $filesystem = new Filesystem();
                $filesystem->mkdir($destination);
            } catch (\Throwable $throwable) {
                throw new \RuntimeException('Could not create directory ' . $directory, 0, $throwable);
            }
        }

        $target = \fopen($destination . DIRECTORY_SEPARATOR . $this->getName(), 'wb');
        try {
            \fseek($source, 0);
            while (!\feof($source)) {
                // Read in 1K chunks.
                // Could make this larger, but as a rule of thumb I'd keep it to 1/4 of php memory_limit.
                if (false === ($chunk = \fread($source, 1024))) {
                    throw new \RuntimeException('Could read from stream of ' . $this->getName());
                }
                if (false === \fwrite($target, $chunk)) {
                    throw new \RuntimeException('Could not save to ' . $this->getName());
                }
            }
        } finally {
            \fclose($target);
        }
    }
}
