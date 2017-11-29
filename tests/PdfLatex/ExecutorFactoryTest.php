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

namespace CyberSpectrum\PdfLatexBundle\Test\PdfLatex;

use CyberSpectrum\PdfLatexBundle\PdfLatex\Executor;
use CyberSpectrum\PdfLatexBundle\PdfLatex\ExecutorFactory;
use CyberSpectrum\PdfLatexBundle\Test\TempDirTestCase;

/**
 * This tests the ExecutorFactory.
 */
class ExecutorFactoryTest extends TempDirTestCase
{
    /**
     * Test that the class can be instantiated.
     *
     * @return void
     */
    public function testCanBeInstantiated()
    {
        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\PdfLatex\ExecutorFactory',
            new ExecutorFactory('/bin/false')
        );
    }

    /**
     * Test create an executor.
     *
     * @return void
     */
    public function testCreatesExecutor()
    {
        $tmpDir = $this->getTempDir();
        touch($tmpDir . DIRECTORY_SEPARATOR . 'foo.tex');

        $factory = new ExecutorFactory('/bin/false');
        $this->assertInstanceOf(
            Executor::class,
            $factory->createExecutor($tmpDir, 'foo.tex')
        );
    }
}
