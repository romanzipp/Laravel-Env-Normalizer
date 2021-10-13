<?php

namespace romanzipp\EnvNormalizer\Test;

use romanzipp\EnvNormalizer\Services\NormalizerService;

class NormalizerTest extends TestCase
{
    public function testServiceCanCreate()
    {
        $service = new NormalizerService(__DIR__ . '/Support/.env.example', [__DIR__ . '/Support/out/.env']);

        $service->normalize();
    }
}
