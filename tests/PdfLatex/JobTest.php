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

namespace CyberSpectrum\PdfLatexBundle\Test\PdfLatex;

use CyberSpectrum\PdfLatexBundle\PdfLatex\File\FileInterface;
use CyberSpectrum\PdfLatexBundle\PdfLatex\Job;
use PHPUnit\Framework\TestCase;

/**
 * This tests the PdfLatexJob.
 *
 * @covers \CyberSpectrum\PdfLatexBundle\PdfLatex\Job
 */
class JobTest extends TestCase
{
    /**
     * Test that the class can be instantiated.
     *
     * @return void
     */
    public function testCanBeInstantiated()
    {
        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\PdfLatex\Job',
            new Job($this->mockTexFile())
        );
    }

    /**
     * Test that the class can be instantiated.
     *
     * @return void
     */
    public function testInstantiationThrowsExceptionForNonTexFile()
    {
        $mock = $this->getMockForAbstractClass(FileInterface::class);
        $mock->method('getName')->willReturn('foo.bar');

        $this->expectException('File foo.bar does not have file extension ".tex"');
        $this->expectException(\InvalidArgumentException::class);

        new Job($mock);
    }

    /**
     * Test that the getter returns the input file.
     *
     * @return void
     */
    public function testTexFileIsReturned()
    {
        $mock = $this->mockTexFile();
        $job  = new Job($mock);

        $this->assertSame($mock, $job->getTexFile());
    }

    /**
     * Test that the assets array is empty when none have been added.
     *
     * @return void
     */
    public function testEmptyAssetsArrayWhenNoneAdded()
    {
        $job = new Job($this->mockTexFile());

        $this->assertSame([], $job->getAssets());
    }

    /**
     * Test that assets can be added and retrieved.
     *
     * @return void
     */
    public function testCanAddAndRetrieveAssets()
    {
        $job    = new Job($this->mockTexFile());
        $assets = [$this->mockTexFile(), $this->mockTexFile()];

        $this->assertSame($job, $job->addAsset($assets[0]));
        $this->assertSame($job, $job->addAsset($assets[1]));

        $this->assertSame($assets, $job->getAssets());
    }

    /**
     * Mock a .tex file.
     *
     * @return FileInterface
     */
    private function mockTexFile(): FileInterface
    {
        $mock = $this->getMockForAbstractClass(FileInterface::class);
        $mock->method('getName')->willReturn('foo.tex');

        return $mock;
    }
}
