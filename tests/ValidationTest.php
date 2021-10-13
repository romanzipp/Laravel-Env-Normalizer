<?php

namespace romanzipp\EnvNormalizer\Test;

use romanzipp\EnvNormalizer\Services\NormalizerService;

class ValidationTest extends TestCase
{
    public function testFileNotFound()
    {
        $this->expectException(\InvalidArgumentException::class);

        new NormalizerService(__DIR__ . '/Support/_missing', [__DIR__ . '/Support/out/.env']);
    }

    public function testOutFileNotFound()
    {
        $this->expectException(\InvalidArgumentException::class);

        new NormalizerService(__DIR__ . '/Support/.env', [__DIR__ . '/Support/_missing']);
    }

    public function testServiceCanCreate()
    {
        $service = new NormalizerService(__DIR__ . '/Support/.env.example', [__DIR__ . '/Support/out/.env']);

        self::assertInstanceOf(NormalizerService::class, $service);
    }
}
