<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\Helper;

/**
 * This class pre-processes text for usage in TeX documents.
 */
class TextUtils
{
    /** List of UTF-8 chars and their TeX representation. */
    public static array $charMap = [
        '\\' => '\\backslash{}',
        '{'  => '\\{',
        '}'  => '\\}',
        '%'  => '\\%',
        '$'  => '\\$',
        '&'  => '\\&',
        '€'  => '\\texteuro{}',
        '§'  => '\\S',
        '©'  => '',
        '¤'  => '',
        '"'  => '“',
        '#'  => '\\#',
        '_'  => '\\_',
        '^'  => '\\^{}',
        '°'  => '$^{\\circ}$',
        '>'  => '\\textgreater{}',
        '<'  => '\\textless{}',
        '~'  => '\\textasciitilde{}',
        '²'  => '\\textsuperscript{2}',
        '³'  => '\\textsuperscript{3}',
        'À'  => '\\`A',
        'Á'  => '\\\'A',
        'Â'  => '\\^A',
        'Ã'  => '\\~A',
        'Ä'  => '\\"A',
        'Ç'  => '\\c{C}',
        'È'  => '\\`E',
        'É'  => '\\\'E',
        'Ê'  => '\\^E',
        'Ë'  => '\\"E',
        'Ì'  => '\\`I',
        'Í'  => '\\\'I',
        'Î'  => '\\^I',
        'Ï'  => '\\"I',
        'Ò'  => '\\`O',
        'Ó'  => '\\\'O',
        'Ô'  => '\\^O',
        'Õ'  => '\\~O',
        'Ö'  => '\\"O',
        'Ø'  => '{\\O}',
        'Ù'  => '\\`U',
        'Ú'  => '\\\'U',
        'Û'  => '\\^U',
        'Ü'  => '\\"U',
        'ß'  => '{\\ss}',
        'à'  => '\\`a',
        'á'  => '\\\'a',
        'â'  => '\\^a',
        'ã'  => '\\~a',
        'ä'  => '\\"a',
        'ç'  => '\\c{c}',
        'è'  => '\\`e',
        'é'  => '\\\'e',
        'ê'  => '\\^e',
        'ë'  => '\\"e',
        'ì'  => '\\`i',
        'í'  => '\\\'i',
        'î'  => '\\^i',
        'ï'  => '\\"i',
        'ñ'  => '\\~n',
        'ò'  => '\\`o',
        'ó'  => '\\\'o',
        'ô'  => '\\^o',
        'õ'  => '\\~o',
        'ö'  => '\\"o',
        'ø'  => '{\\o}',
        'ù'  => '\\`u',
        'ú'  => '\\\'u',
        'û'  => '\\^u',
        'ü'  => '\\"u',
        'Ă'  => '\\u{A}',
        // 'Ă'  => '\\v{A}',
        'ă'  => '\\u{a}',
        // 'ă'  => '\\v{a}',
        'Ő'  => '\\H{O}',
        'ő'  => '\\H{o}',
        'Š'  => '\\v{S}',
        'š'  => '\\v{s}',
        'Ŭ'  => '\\u{U}',
        'ŭ'  => '\\u{u}',
        'Ű'  => '\\H{U}',
        'ű'  => '\\H{u}',
        'Ș'  => '\\c{S}',
        'ș'  => '\\c{s}',
    ];

    /**
     * Parse the text and replace known special latex characters correctly.
     *
     * @param string $text          The string that needs to be parsed.
     * @param bool   $escapeNewLine If set, newline characters will be replaced by LaTeX entities (default false).
     *
     * @return string
     */
    public function parseText(string $text, bool $escapeNewLine = false): string
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
