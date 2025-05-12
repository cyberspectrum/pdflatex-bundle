<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\Test\Helper;

use CyberSpectrum\PdfLatexBundle\Helper\TextUtils;
use PHPUnit\Framework\TestCase;

/**
 * This tests the TextUtils class.
 *
 * @covers \CyberSpectrum\PdfLatexBundle\Helper\TextUtils
 */
class TextUtilsTest extends TestCase
{
    /** Provide datasets for the parseText test. */
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
            'do not touch numeric' => [
                '74889',
                '74889',
            ],
            'superscript' => [
                '\\textsuperscript{2}',
                '&sup2;',
            ],
        ];
    }

    /**
     * Test the parseText() method.
     *
     * @param string $expected The expected result.
     * @param string $input    The input string.
     *
     * @dataProvider textParsingProvider
     */
    public function testTextParsing(string $expected, string $input): void
    {
        $utils = new TextUtils();

        $this->assertSame($expected, $utils->parseText($input));
    }

    /** Test the parseText() method with newline escaping. */
    public function testTextParsingWithNewlineEscaping(): void
    {
        $utils = new TextUtils();

        $this->assertSame('Hello\\newline{}there', $utils->parseText("Hello\nthere", true));
    }
}
