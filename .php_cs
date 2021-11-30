<?php

$header = <<<'EOF'
This file is part of cyberspectrum/pdflatex-bundle.

(c) CyberSpectrum <http://www.cyberspectrum.de/>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.

This project is provided in good faith and hope to be usable by anyone.

EOF;

$config = PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@PHP56Migration' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'align_multiline_comment' => true,
        'array_syntax' => ['syntax' => 'short'],
        'blank_line_before_statement' => true,
        'binary_operator_spaces' => [
            'default' => 'align_single_space'
        ],
        'combine_consecutive_unsets' => true,
        'concat_space' => ['spacing' => 'one'],
        'declare_equal_normalize' => ['space' => 'single'],
        'declare_strict_types' => true,
        // Can not use so far because it strips the doc comment tags.
        /*
        'header_comment' => [
            'commentType' => 'PHPDoc',
            'header' => $header,
            'location' => 'after_open'
        ],
        */
        'header_comment' => false,
        'heredoc_to_nowdoc' => true,
        'list_syntax' => ['syntax' => 'long'],
        'method_argument_space' => ['ensure_fully_multiline' => true],
        'no_extra_consecutive_blank_lines' => [
            'tokens' => [
                'break',
                'continue',
                'extra',
                'return',
                'throw',
                'use',
                'parenthesis_brace_block',
                'square_brace_block',
                'curly_brace_block'
            ]
        ],
        'no_null_property_initialization' => true,
        'no_short_echo_tag' => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'php_unit_strict' => true,
        'php_unit_test_class_requires_covers' => true,
        'phpdoc_annotation_without_dot' => false,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_no_empty_return' => false,
        'phpdoc_no_package' => false,
        'phpdoc_order' => true,
        'phpdoc_separation' => false,
        // Kills @var annotations otherwise.
        // 'phpdoc_to_comment' => false,
        'phpdoc_types_order' => true,
        'semicolon_after_instruction' => true,
        'single_line_comment_style' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'yoda_style' => true
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude('tests/fixtures')
            ->in(__DIR__)
    );

PhpCsFixer\FixerFactory::create()
    ->registerBuiltInFixers()
    ->registerCustomFixers($config->getCustomFixers())
    ->useRuleSet(new PhpCsFixer\RuleSet($config->getRules()));

return $config;
