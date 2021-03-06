<?php

namespace romanzipp\EnvNormalizer\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use romanzipp\EnvNormalizer\Services\NormalizerService;
use Symfony\Component\Finder\Finder;

class NormalizeEnvFilesCommand extends Command
{
    protected $signature = 'env:normalize {--reference=.env.example : The reference example file}
                                          {--target=* : Target .env files}
                                          {--backup : Create backups for target files}
                                          {--auto : Add all other .env.* files to the target list}
                                          {--path= : Base path to look for files}
                                          {--dry : Only print changes to console}';

    public function handle(): int
    {
        $service = new NormalizerService(
            $this->basePath($this->option('reference')),
            $this->getTargetFiles()
        );

        if ($this->option('backup')) {
            try {
                $service->withBackup();
            } catch (\InvalidArgumentException $exception) {
                $this->warn($exception->getMessage());

                if ( ! $this->confirm('Overwrite?')) {
                    $this->info('Exiting');

                    return 0;
                }

                $service->withBackup(true);
            }
        }

        if ($this->option('dry')) {
            foreach ($service->dry() as $content) {
                $this->line((string) $content);
            }
        } else {
            foreach ($service->normalize() as $content) {
                $this->info(sprintf('Normalized %s', $content->getTitle()));
            }
        }

        return 0;
    }

    /**
     * @return string[]
     */
    public function getTargetFiles(): array
    {
        $targets = [];

        if ($this->option('auto')) {
            $finder = new Finder();

            foreach ($finder->in($this->basePath())->ignoreVCS(true)->ignoreDotFiles(false)->files()->name('/^.env/') as $target) {
                if ($target->getFilename() === $this->option('reference')) {
                    continue;
                }

                $targets[] = $target->getFilename();
            }
        } else {
            if (empty($this->option('target'))) {
                $targets[] = '.env';
            } else {
                $targets = is_array($this->option('target')) ? $this->option('target') : [$this->option('target')];
            }
        }

        return array_map(fn (string $path) => $this->basePath($path), $targets);
    }

    public function basePath(?string $path = null): string
    {
        if ( ! $this->option('path')) {
            return base_path($path);
        }

        return (Str::endsWith($this->option('path'), '/') ? Str::replaceLast('/', '', $this->option('path')) : $this->option('path')) . '/' . $path;
    }
}
