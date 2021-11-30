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

namespace CyberSpectrum\PdfLatexBundle\Test\Twig;

use CyberSpectrum\PdfLatexBundle\Helper\TextUtils;
use CyberSpectrum\PdfLatexBundle\Twig\Extension;
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
     * @return void
     *
     * @covers \CyberSpectrum\PdfLatexBundle\Twig\Extension::__construct
     */
    public function testCanBeInstantiated()
    {
        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\Twig\Extension',
            new Extension()
        );
    }
    /**
     * Test that the class can be instantiated with text utils instance.
     *
     * @return void
     *
     * @covers \CyberSpectrum\PdfLatexBundle\Twig\Extension::__construct
     */
    public function testCanBeInstantiatedWithArgument()
    {
        $this->assertInstanceOf(
            'CyberSpectrum\PdfLatexBundle\Twig\Extension',
            $extension = new Extension($utils = new TextUtils())
        );

        $reflection = new \ReflectionProperty(Extension::class, 'utils');
        $reflection->setAccessible(true);
        $this->assertSame($utils, $reflection->getValue($extension));
    }

    /**
     * Data provider for the filter tests.
     *
     * @return array
     */
    public function filterTestProvider()
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
     * @return void
     *
     * @dataProvider filterTestProvider
     *
     * @covers \CyberSpectrum\PdfLatexBundle\Twig\Extension::texify
     * @covers \CyberSpectrum\PdfLatexBundle\Twig\Extension::texifyAll
     */
    public function testFilter($expected, $template, $context)
    {
        $twig = new Environment(new ArrayLoader(['template.tex.twig' => $template]), ['autoescape' => 'tex']);

        $twig->addExtension($extension = new Extension());

        $this->assertSame($expected, $twig->render('template.tex.twig', $context));
    }

    /**
     * Test that the escape method is called.
     *
     * @return void
     */
    public function testEscape()
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

    /**
     * Test that empty values are not being passed.
     *
     * @return void
     */
    public function testTexifyDoesNotPassEmptyString()
    {
        $utils = $this->getMockBuilder(TextUtils::class)->setMethods(['parseText'])->getMock();
        $utils->expects($this->never())->method('parseText');

        $extension = new Extension($utils);
        $extension->texify('');
    }

    /**
     * Test that empty values are not being passed.
     *
     * @return void
     */
    public function testTexifyAllDoesNotPassEmptyString()
    {
        $utils = $this->getMockBuilder(TextUtils::class)->setMethods(['parseText'])->getMock();
        $utils->expects($this->never())->method('parseText');

        $extension = new Extension($utils);
        $extension->texifyAll('');
    }

    /**
     * Test that the escape method is called.
     *
     * @return void
     */
    public function testDoesNotEscapeWhenDisablingInTemplate()
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

    /**
     * Test that the escape method is called.
     *
     * @return void
     */
    public function testDoesEscapeWhenUsingHtml()
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

    /**
     * Test that the escape method is called.
     *
     * @return void
     */
    public function testDoesNotEscapeWhenUsingHtmlInTemplate()
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
