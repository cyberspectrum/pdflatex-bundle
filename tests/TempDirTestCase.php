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
    /**
     * The temp dir.
     *
     * @var string
     */
    private $tempDir;

    /**
     * The filesystem.
     *
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->tempDir    = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('pdflatex-bundle');
        $this->fileSystem = new Filesystem();

        $this->fileSystem->mkdir($this->tempDir);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->fileSystem->remove($this->tempDir);
        parent::tearDown();
    }

    /**
     * Return the temporary directory path.
     *
     * @return string
     */
    protected function getTempDir(): string
    {
        return $this->tempDir;
    }
}
