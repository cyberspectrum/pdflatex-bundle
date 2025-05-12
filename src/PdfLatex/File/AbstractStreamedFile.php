<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\PdfLatex\File;

use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

use function fclose;
use function feof;
use function fopen;
use function fread;
use function fseek;
use function fwrite;

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
     * @throws RuntimeException When anything goes wrong.
     */
    protected function save($source, string $destination): void
    {
        if (!empty($directory = $this->getDirectory())) {
            $destination .= DIRECTORY_SEPARATOR . $directory;

            try {
                $filesystem = new Filesystem();
                $filesystem->mkdir($destination);
            } catch (Throwable $throwable) {
                throw new RuntimeException('Could not create directory ' . $directory, 0, $throwable);
            }
        }

        $target = fopen($destination . DIRECTORY_SEPARATOR . $this->getName(), 'wb');
        if (false === $target) {
            throw new RuntimeException('Could not open ' . $destination . DIRECTORY_SEPARATOR . $this->getName());
        }

        try {
            fseek($source, 0);
            while (!feof($source)) {
                // Read in 1K chunks.
                // Could make this larger, but as a rule of thumb I'd keep it to 1/4 of php memory_limit.
                if (false === ($chunk = fread($source, 1024))) {
                    throw new RuntimeException('Could read from stream of ' . $this->getName());
                }
                if (false === fwrite($target, $chunk)) {
                    throw new RuntimeException('Could not save to ' . $this->getName());
                }
            }
        } finally {
            fclose($target);
        }
    }
}
