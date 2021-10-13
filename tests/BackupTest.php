<?php

namespace romanzipp\EnvNormalizer\Test;

use romanzipp\EnvNormalizer\Services\NormalizerService;

class BackupTest extends TestCase
{
    public function testNoBackup()
    {
        $service = new NormalizerService(__DIR__ . '/Support/.env.example', [__DIR__ . '/Support/out/.env']);
        $service->normalize();

        if (method_exists(self::class, 'assertFileDoesNotExist')) {
            self::assertFileDoesNotExist(__DIR__ . '/Support/out/.env.bak');
        } else {
            self::assertFileNotExists(__DIR__ . '/Support/out/.env.bak');
        }
    }

    public function testBackup()
    {
        $service = new NormalizerService(__DIR__ . '/Support/.env.example', [__DIR__ . '/Support/out/.env']);
        $service->withBackup();
        $service->normalize();

        self::assertFileExists(__DIR__ . '/Support/out/.env.bak');
    }

    public function testBackupMultipleFiles()
    {
        $service = new NormalizerService(__DIR__ . '/Support/.env.example', [__DIR__ . '/Support/out/.env', __DIR__ . '/Support/out/.env.local']);
        $service->withBackup();
        $service->normalize();

        self::assertFileExists(__DIR__ . '/Support/out/.env.bak');
        self::assertFileExists(__DIR__ . '/Support/out/.env.local.bak');
    }
}
