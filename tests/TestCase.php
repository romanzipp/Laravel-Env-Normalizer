<?php

namespace romanzipp\EnvNormalizer\Test;

use Orchestra\Testbench\TestCase as BaseTestCase;
use romanzipp\EnvNormalizer\Providers\EnvNormalizerServiceProvider;
use romanzipp\EnvNormalizer\Services\NormalizerService;

abstract class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        foreach (scandir(__DIR__ . '/Support') as $item) {
            if (in_array($item, ['.', '..', 'out'])) {
                continue;
            }

            copy(__DIR__ . '/Support/' . $item, __DIR__ . '/Support/out/' . $item);
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        foreach (scandir(__DIR__ . '/Support/out') as $item) {
            if (in_array($item, ['.', '..', '.gitignore'])) {
                continue;
            }

            unlink(__DIR__ . '/Support/out/' . $item);
        }
    }

    protected function newService(): NormalizerService
    {
        return new NormalizerService(__DIR__ . '/Support/.env.example', [__DIR__ . '/Support/out/.env']);
    }

    protected function getPackageProviders($app): array
    {
        return [
            EnvNormalizerServiceProvider::class,
        ];
    }
}
