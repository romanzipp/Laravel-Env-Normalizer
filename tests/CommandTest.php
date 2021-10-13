<?php

namespace romanzipp\EnvNormalizer\Test;

class CommandTest extends TestCase
{
    public function testCommand()
    {
        $this->artisan('env:normalize', [
            '--path' => __DIR__ . '/Support/out/',
        ]);

        self::assertFileExists(__DIR__ . '/Support/out/.env');
    }

    public function testRefNotFound()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('File not found');

        $this->artisan('env:normalize', [
            '--path' => __DIR__ . '/Support/out/',
            '--reference' => '.env.missing',
            '--target' => '.env',
        ]);
    }

    public function testTargetNotFound()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('File not found');

        $this->artisan('env:normalize', [
            '--path' => __DIR__ . '/Support/out/',
            '--target' => '.env.missing',
        ]);
    }

    public function testAuto()
    {
        $this->artisan('env:normalize', [
            '--path' => __DIR__ . '/Support/out/',
            '--auto' => true,
        ]);

        self::assertFileExists(__DIR__ . '/Support/out/.env');
    }

    public function testDry()
    {
        $this
            ->artisan('env:normalize', [
                '--path' => __DIR__ . '/Support/out/',
                '--reference' => '.env.example',
                '--target' => '.env',
                '--dry' => true,
            ])
            ->expectsOutput(implode(PHP_EOL, [
                'BASE_URL=http://example.com',
                '',
                '# Database',
                '',
                'DB_HOST=10.0.0.10',
                'DB_USER=prod',
                'DB_PASSWORD=123456',
                '',
                '# Mail',
                '',
                'MAIL_CONNECTION=foo',
                '',
                '# Additional',
                '',
                'MAIL_FROM=mail@example.com',
            ]))
            ->run();
    }
}
