<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\Test\Twig;

use CyberSpectrum\PdfLatexBundle\Helper\TextUtils;
use CyberSpectrum\PdfLatexBundle\Helper\TextUtilsInterface;
use CyberSpectrum\PdfLatexBundle\Twig\Extension;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * This tests the twig extension.
 *
 * @covers \CyberSpectrum\PdfLatexBundle\Twig\Extension
 */
class ExtensionTest extends TestCase
{
    /**
     * Test that the class can be instantiated.
     *
     * @covers \CyberSpectrum\PdfLatexBundle\Twig\Extension::__construct
     */
    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\Twig\Extension',
            new Extension()
        );
    }
    /**
     * Test that the class can be instantiated with text utils instance.
     *
     * @covers \CyberSpectrum\PdfLatexBundle\Twig\Extension::__construct
     */
    public function testCanBeInstantiatedWithArgument(): void
    {
        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\Twig\Extension',
            $extension = new Extension($utils = new TextUtils())
        );

        $reflection = new \ReflectionProperty(Extension::class, 'utils');
        $this->assertSame($utils, $reflection->getValue($extension));
    }

    /** Data provider for the filter tests. */
    public static function filterTestProvider(): iterable
    {
        return [
            'Should escape umlauts' => [
                'expected' => 'An \"A',
                'template' => 'An {{ value | texify }}',
                'context'  => ['value' => 'Ä']
            ],
            'Should escape umlauts with filter only once' => [
                'expected' => '\"A',
                'template' => '{{ value | texify }}',
                'context'  => ['value' => 'Ä']
            ],
            'Should escape backslash' => [
                'expected' => '\\backslash{}',
                'template' => '{{ value | texify }}',
                'context'  => ['value' => '\\']
            ],
            'Should escape line breaks' => [
                'expected' => 'Text\newline{}more text.',
                'template' => '{{ value | texify_all }}',
                'context'  => ['value' => "Text\nmore text."]
            ],
        ];
    }

    /**
     * Test that the filter work.
     *
     * @param string $expected The expected output.
     * @param string $template The template content.
     * @param array  $context  The context.
     *
     * @covers \CyberSpectrum\PdfLatexBundle\Twig\Extension::texify
     * @covers \CyberSpectrum\PdfLatexBundle\Twig\Extension::texifyAll
     */
    #[DataProvider('filterTestProvider')]
    public function testFilter(string $expected, string $template, array $context): void
    {
        $twig = new Environment(new ArrayLoader(['template.tex.twig' => $template]), ['autoescape' => 'tex']);

        $twig->addExtension(new Extension());

        $this->assertSame($expected, $twig->render('template.tex.twig', $context));
    }

    /** Test that the escape method is called. */
    public function testEscape(): void
    {
        $twig = new Environment(
            new ArrayLoader(['template.tex.twig' => 'content{{backslash}}value']),
            ['autoescape' => 'tex']
        );

        $twig->addExtension($extension = new Extension());
        $extension->addEscaperTo($twig);

        $this->assertSame(
            'content\\backslash{}value',
            $twig->render('template.tex.twig', ['backslash' => '\\'])
        );
    }

    /** Test that empty values are not being passed. */
    public function testTexifyDoesNotPassEmptyString(): void
    {
        $utils = $this->getMockBuilder(TextUtilsInterface::class)->onlyMethods(['parseText'])->getMock();
        $utils->expects($this->never())->method('parseText');

        $extension = new Extension($utils);
        self::assertSame('', $extension->texify(''));
    }

    /** Test that empty values are not being passed. */
    public function testTexifyAllDoesNotPassEmptyString(): void
    {
        $utils = $this->getMockBuilder(TextUtilsInterface::class)->onlyMethods(['parseText'])->getMock();
        $utils->expects($this->never())->method('parseText');

        $extension = new Extension($utils);
        self::assertSame('', $extension->texify(''));
    }

    /** Test that the escape method is called. */
    public function testDoesNotEscapeWhenDisablingInTemplate(): void
    {
        $twig = new Environment(
            new ArrayLoader(['template.tex.twig' => '
{% autoescape false %}
content{{backslash}}value
content{{backslash|texify}}value
{% endautoescape %}
']),
            ['autoescape' => 'tex']
        );

        $twig->addExtension($extension = new Extension());
        $extension->addEscaperTo($twig);

        $this->assertSame(
            '
content\\value
content\\backslash{}value
',
            $twig->render('template.tex.twig', ['backslash' => '\\'])
        );
    }

    /** Test that the escape method is called. */
    public function testDoesEscapeWhenUsingHtml(): void
    {
        $twig = new Environment(
            new ArrayLoader(['template.tex.twig' => '
content{{ampersand}}value
content{{ampersand|texify}}value
']),
            ['autoescape' => 'html']
        );

        $twig->addExtension($extension = new Extension());
        $extension->addEscaperTo($twig);

        $this->assertSame(
            '
content&amp;value
content\\&amp;value
',
            $twig->render('template.tex.twig', ['ampersand' => '&'])
        );
    }

    /** Test that the escape method is called. */
    public function testDoesNotEscapeWhenUsingHtmlInTemplate(): void
    {
        $twig = new Environment(
            new ArrayLoader(['template.tex.twig' => '
{% autoescape \'html\' %}
content{{ampersand}}value
content{{ampersand|texify}}value
{% endautoescape %}
']),
            ['autoescape' => 'tex']
        );

        $twig->addExtension($extension = new Extension());
        $extension->addEscaperTo($twig);

        $this->assertSame(
            '
content&amp;value
content\\&amp;value
',
            $twig->render('template.tex.twig', ['ampersand' => '&'])
        );
    }
}
