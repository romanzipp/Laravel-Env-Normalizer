<?php

namespace romanzipp\EnvNormalizer\Providers;

use Illuminate\Support\ServiceProvider;
use romanzipp\EnvNormalizer\Console\Commands\NormalizeEnvFilesCommand;
use romanzipp\EnvNormalizer\Services\NormalizerService;

class EnvNormalizerServiceProvider extends ServiceProvider
{
    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [NormalizerService::class];
    }

    public function register()
    {
        $this->commands([
            NormalizeEnvFilesCommand::class,
        ]);
    }
}
