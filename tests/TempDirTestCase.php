<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\Test;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * This test case provides means to handle temporary files.
 *
 * @coversNothing
 */
class TempDirTestCase extends TestCase
{
    /** The temp dir. */
    private string $tempDir;

    /** The filesystem. */
    private Filesystem $fileSystem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tempDir    = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('pdflatex-bundle');
        $this->fileSystem = new Filesystem();

        $this->fileSystem->mkdir($this->tempDir);
    }

    protected function tearDown(): void
    {
        $this->fileSystem->remove($this->tempDir);
        parent::tearDown();
    }

    /** Return the temporary directory path. */
    protected function getTempDir(): string
    {
        return $this->tempDir;
    }
}
