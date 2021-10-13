<?php

namespace romanzipp\EnvNormalizer\Services;

use SplFileInfo;

class NormalizerService
{
    private bool $createBackup = true;

    /**
     * Reference .env.example file.
     *
     * @var \SplFileInfo
     */
    private SplFileInfo $reference;

    /**
     * File to write.
     *
     * @var \SplFileInfo[]
     */
    private array $output;

    public function __construct(string $referencePath, array $outputPaths)
    {
        $this->reference = new SplFileInfo($referencePath);

        foreach ($outputPaths as $outputPath) {
            $this->output[] = new SplFileInfo($outputPath);
        }

        $this->validate();
    }

    private function validate(): void
    {
        foreach ([$this->reference, ...$this->output] as $file) {
            /**
             * @var \SplFileInfo $file
             */
            if ( ! $file->isFile()) {
                throw new \InvalidArgumentException('Paths must be files');
            }
        }

        foreach ($this->output as $file) {
            if ( ! $file->isWritable()) {
                throw new \InvalidArgumentException('Can not write output files');
            }
        }
    }

    public function normalize(): void
    {
        $reference = explode(PHP_EOL, file_get_contents($this->reference));

        if ($this->createBackup) {
        }

        foreach ($this->output as $item) {
        }
    }
}
