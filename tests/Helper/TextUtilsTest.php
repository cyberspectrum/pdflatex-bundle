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

namespace CyberSpectrum\PdfLatexBundle\Test\Helper;

use CyberSpectrum\PdfLatexBundle\Helper\TextUtils;
use PHPUnit\Framework\TestCase;

/**
 * This tests the TextUtils class.
 */
class TextUtilsTest extends TestCase
{
    /**
     * Provide datasets for the parseText test.
     *
     * @return array
     */
    public function textParsingProvider(): array
    {
        return [
            'decode named entity' => [
                '\"a',
                '&auml;',
            ],
            'decode 2byte UTF-8 entity' => [
                'Љ',
                '&#1033;',
            ],
            'escape special tex chars' => [
                '\\backslash{}\\{\\}',
                '\\{}',
            ],
        ];
    }

    /**
     * Test the parseText() method
     *
     * @param string $expected The expected result.
     * @param string $input    The input string.
     *
     * @return void
     *
     * @dataProvider textParsingProvider
     */
    public function testTextParsing(string $expected, string $input)
    {
        $utils = new TextUtils();

        $this->assertSame($expected, $utils->parseText($input));
    }

    /**
     * Test the parseText() method with newline escaping.
     *
     * @return void
     */
    public function testTextParsingWithNewlineEscaping()
    {
        $utils = new TextUtils();

        $this->assertSame('Hello\\newline{}there', $utils->parseText("Hello\nthere", true));
    }
}
