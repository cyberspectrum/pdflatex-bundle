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

namespace CyberSpectrum\PdfLatexBundle\Helper;

/**
 * This class pre-processes text for usage in TeX documents.
 */
class TextUtils
{
    /**
     * List of UTF-8 chars and their TeX representation.
     *
     * @var array
     */
    public static $charMap = [
        '\\' => '\\backslash{}',
        '{'  => '\\{',
        '}'  => '\\}',
        '%'  => '\\%',
        '$'  => '\\$',
        '&'  => '\\&',
        '€' => '\\texteuro{}',
        '§' => '\\S',
        '©' => '',
        '¤' => '',
        '"' => '“',
        '#' => '\\#',
        '_' => '\\_',
        '^' => '\\^{}',
        '°' => '\$^{\\circ}\$',
        '>' => '\\textgreater{}',
        '<' => '\\textless{}',
        '~' => '\\textasciitilde{}',
        'ä' => '\\"a',
        'á' => '\\\'a',
        'à' => '\\`a',
        'â' => '\\^a',
        'ã' => '\\~a',
        'Ä' => '\\"A',
        'Á' => '\\\'A',
        'À' => '\\`A',
        'Â' => '\\^A',
        'Ã' => '\\~A',
        'ë' => '\\"e',
        'é' => '\\\'e',
        'è' => '\\`e',
        'ê' => '\\^e',
        'Ë' => '\\"E',
        'É' => '\\\'E',
        'È' => '\\`E',
        'Ê' => '\\^E',
        'ï' => '\\"i',
        'í' => '\\\'i',
        'ì' => '\\`i',
        'î' => '\\^i',
        'Ï' => '\\"I',
        'Í' => '\\\'I',
        'Ì' => '\\`I',
        'Î' => '\\^I',
        'ö' => '\\"o',
        'ó' => '\\\'o',
        'ő' => '\\H{o}',
        'ò' => '\\`o',
        'ô' => '\\^o',
        'õ' => '\\~o',
        'Ö' => '\\"O',
        'Ó' => '\\\'O',
        'Ő' => '\\H{O}',
        'Ò' => '\\`O',
        'Ô' => '\\^O',
        'Õ' => '\\~O',
        'ü' => '\\"u',
        'ú' => '\\\'u',
        'ű' => '\\H{u}',
        'ù' => '\\`u',
        'û' => '\\^u',
        'Ü' => '\\"U',
        'Ú' => '\\\'U',
        'Ű' => '\\H{U}',
        'Ù' => '\\`U',
        'Û' => '\\^U',
        'ñ' => '\\~n',
        'ß' => '{\\ss}',
        'ç' => '\\c{c}',
        'Ç' => '\\c{C}',
        'ș' => '\\c{s}',
        'Ș' => '\\c{S}',
        'ŭ' => '\\u{u}',
        'Ŭ' => '\\u{U}',
        'ă' => '\\u{a}',
        'Ă' => '\\u{A}',
        'ă' => '\\v{a}',
        'Ă' => '\\v{A}',
        'š' => '\\v{s}',
        'Š' => '\\v{S}',
        'Ø' => '{\\O}',
        'ø' => '{\\o}',
        '&sup2;', '\\textsuperscript{2}',
        '&sup3;', '\\textsuperscript{3}',
        '²', '\\textsuperscript{2}',
        '³', '\\textsuperscript{3}',
    ];

    /**
     * Parse the text and replace known special latex characters correctly
     *
     * @param string  $text          The string that needs to be parsed.
     * @param boolean $escapeNewLine If set, newline characters will be replaced by LaTeX entities (default false).
     *
     * @return mixed
     */
    public function parseText($text, $escapeNewLine = false)
    {
        // Try to replace HTML entities
        $text = html_entity_decode($text, ENT_QUOTES, 'utf-8');
        $text = strtr($text, self::$charMap);

        // New lines if required.
        if ($escapeNewLine) {
            $text = str_replace('\\n', '\\newline{}', $text);
            $text = str_replace(PHP_EOL, '\\newline{}', $text);
        }

        return $text;
    }
}
