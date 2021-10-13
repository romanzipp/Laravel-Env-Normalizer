<?php

namespace romanzipp\EnvNormalizer\Test;

use romanzipp\EnvNormalizer\Services\Content;

class DryNormalizerTest extends TestCase
{
    public function testKeepsValuesFromEmptyRef()
    {
        $content = $this->newService()->normalizeContent(
            new Content(implode(PHP_EOL, [
                'FIRST=',
                'SECOND=',
            ])),
            new Content(implode(PHP_EOL, [
                'FIRST=foo',
                'SECOND=bar',
            ]))
        );

        self::assertSame(implode(PHP_EOL, [
            'FIRST=foo',
            'SECOND=bar',
        ]), (string) $content);
    }

    public function testOverrideRefValues()
    {
        $content = $this->newService()->normalizeContent(
            new Content(implode(PHP_EOL, [
                'FIRST=foo',
                'SECOND=foo',
            ])),
            new Content(implode(PHP_EOL, [
                'FIRST=bar',
                'SECOND=bar',
            ]))
        );

        self::assertSame(implode(PHP_EOL, [
            'FIRST=bar',
            'SECOND=bar',
        ]), (string) $content);
    }

    public function testReorderLines()
    {
        $content = $this->newService()->normalizeContent(
            new Content(implode(PHP_EOL, [
                'FIRST=foo',
                'SECOND=bar',
            ])),
            new Content(implode(PHP_EOL, [
                'SECOND=bar',
                'FIRST=foo',
            ]))
        );

        self::assertSame(implode(PHP_EOL, [
            'FIRST=foo',
            'SECOND=bar',
        ]), (string) $content);
    }

    public function testKeepsEmptyLines()
    {
        $content = $this->newService()->normalizeContent(
            new Content(implode(PHP_EOL, [
                'FIRST=',
                '',
                'SECOND=',
            ])),
            new Content(implode(PHP_EOL, [
                'FIRST=foo',
                'SECOND=bar',
            ]))
        );

        self::assertSame(implode(PHP_EOL, [
            'FIRST=foo',
            '',
            'SECOND=bar',
        ]), (string) $content);
    }

    public function testKeepsMultipleEmptyLines()
    {
        $content = $this->newService()->normalizeContent(
            new Content(implode(PHP_EOL, [
                'FIRST=',
                '',
                '',
                '',
                'SECOND=',
            ])),
            new Content(implode(PHP_EOL, [
                'FIRST=foo',
                'SECOND=bar',
            ]))
        );

        self::assertSame(implode(PHP_EOL, [
            'FIRST=foo',
            '',
            '',
            '',
            'SECOND=bar',
        ]), (string) $content);
    }

    public function testKeepsCommentLines()
    {
        $content = $this->newService()->normalizeContent(
            new Content(implode(PHP_EOL, [
                'FIRST=',
                '',
                '# Tests',
                '',
                'SECOND=',
            ])),
            new Content(implode(PHP_EOL, [
                'FIRST=foo',
                'SECOND=bar',
            ]))
        );

        self::assertSame(implode(PHP_EOL, [
            'FIRST=foo',
            '',
            '# Tests',
            '',
            'SECOND=bar',
        ]), (string) $content);
    }

    public function testTrimsWhitespaces()
    {
        $content = $this->newService()->normalizeContent(
            new Content(implode(PHP_EOL, [
                'FIRST=',
                '  ',
                '# Tests  ',
                '',
                'SECOND=',
            ])),
            new Content(implode(PHP_EOL, [
                'FIRST=foo    ',
                'SECOND=bar',
            ]))
        );

        self::assertSame(implode(PHP_EOL, [
            'FIRST=foo',
            '',
            '# Tests',
            '',
            'SECOND=bar',
        ]), (string) $content);
    }

    public function testAppendsAdditionalVariablesOnBottom()
    {
        $content = $this->newService()->normalizeContent(
            $c = new Content(implode(PHP_EOL, [
                'FIRST=',
                '',
                '# Tests',
                '',
                'SECOND=',
            ])),
            new Content(implode(PHP_EOL, [
                'FIRST=foo',
                'THIRD=foobar',
                'SECOND=bar',
            ]))
        );

        self::assertSame(implode(PHP_EOL, [
            'FIRST=foo',
            '',
            '# Tests',
            '',
            'SECOND=bar',
            '',
            '# Not found while normalizing',
            '',
            'THIRD=foobar',
        ]), (string) $content);
    }
}
