<?php

namespace romanzipp\EnvNormalizer\Test\Support;

use romanzipp\EnvNormalizer\Services\Line;
use romanzipp\EnvNormalizer\Test\TestCase;

class LineTest extends TestCase
{
    public function testDetectComments()
    {
        self::assertTrue(Line::contentIsHeader('# Header'));
        self::assertTrue(Line::contentIsHeader('# '));
        self::assertTrue(Line::contentIsHeader('# Ab1#-_.:,;'));

        self::assertFalse(Line::contentIsHeader('#Header'));
        self::assertFalse(Line::contentIsHeader('#BASE_URL='));
    }

    public function testDetectBlank()
    {
        self::assertTrue(Line::contentIsBlank(''));
        self::assertTrue(Line::contentIsBlank(' '));
        self::assertTrue(Line::contentIsBlank('  '));

        self::assertFalse(Line::contentIsBlank('a'));
        self::assertFalse(Line::contentIsBlank('  Test'));
        self::assertFalse(Line::contentIsBlank('  # Test'));
    }

    public function testDetectVariable()
    {
        self::assertSame('FOO', Line::getContentVariable('FOO='));
        self::assertSame('FOO', Line::getContentVariable('FOO=a'));

        self::assertNull(Line::getContentVariable('#FOO'));
        self::assertNull(Line::getContentVariable('#FOO='));
        self::assertNull(Line::getContentVariable('#FOO=a'));
        self::assertNull(Line::getContentVariable('# FOO'));
        self::assertNull(Line::getContentVariable('# FOO='));
        self::assertNull(Line::getContentVariable('# FOO=a'));
        self::assertNull(Line::getContentVariable('FOO'));
        self::assertNull(Line::getContentVariable(' FOO='));
        self::assertNull(Line::getContentVariable(' FOO=a'));
        self::assertNull(Line::getContentVariable('foo'));
        self::assertNull(Line::getContentVariable(' foo='));
        self::assertNull(Line::getContentVariable(' foo=a'));
    }

    public function testDetectValue()
    {
        self::assertSame(null, Line::getContentValue('FOO='));
        self::assertSame(' ', Line::getContentValue('FOO= '));
        self::assertSame('  ', Line::getContentValue('FOO=  '));
        self::assertSame('a', Line::getContentValue('FOO=a'));
        self::assertSame(' a', Line::getContentValue('FOO= a'));
        self::assertSame('1', Line::getContentValue('FOO=1'));
        self::assertSame(' 1', Line::getContentValue('FOO= 1'));
    }
}
