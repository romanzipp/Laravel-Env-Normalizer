<?php

namespace romanzipp\EnvNormalizer\Test;

use romanzipp\EnvNormalizer\Services\NormalizerService;

class NormalizerTest extends TestCase
{
    public function testSingleNormalization()
    {
        $service = new NormalizerService(__DIR__ . '/Support/.env.example', [$target = __DIR__ . '/Support/out/.env']);
        $service->normalize();

        self::assertFileExists($target);
        self::assertSame(implode(PHP_EOL, [
            'BASE_URL=http://example.com',
            '',
            '# Databse',
            '',
            'DB_HOST=10.0.0.10',
            'DB_USER=prod',
            'DB_PASSWORD=123456',
            '',
            '# Mail',
            '',
            'MAIL_CONNECTION=foo',
            '',
            '# Not found while normalizing',
            '',
            'MAIL_FROM=mail@example.com',
        ]), file_get_contents($target));
    }

    public function testMultipleNormalizations()
    {
        $service = new NormalizerService(__DIR__ . '/Support/.env.example', [$firstTarget = __DIR__ . '/Support/out/.env', $secondsTarget = __DIR__ . '/Support/out/.env.local']);
        $service->normalize();

        self::assertFileExists($firstTarget);
        self::assertSame(implode(PHP_EOL, [
            'BASE_URL=http://example.com',
            '',
            '# Databse',
            '',
            'DB_HOST=10.0.0.10',
            'DB_USER=prod',
            'DB_PASSWORD=123456',
            '',
            '# Mail',
            '',
            'MAIL_CONNECTION=foo',
            '',
            '# Not found while normalizing',
            '',
            'MAIL_FROM=mail@example.com',
        ]), file_get_contents($firstTarget));

        self::assertFileExists($secondsTarget);
        self::assertSame(implode(PHP_EOL, [
            '# Databse',
            '',
            '# Mail',
        ]), file_get_contents($secondsTarget));
    }
}
