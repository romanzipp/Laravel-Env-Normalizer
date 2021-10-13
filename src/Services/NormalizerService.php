<?php

namespace romanzipp\EnvNormalizer\Services;

use SplFileInfo;

class NormalizerService
{
    private bool $createBackup = false;

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

    public function withoutBackup(): self
    {
        $this->createBackup = false;

        return $this;
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
        $referenceContent = self::getContents($this->reference);

        if ($this->createBackup) {
            // TODO create backup files
        }

        foreach ($this->output as $file) {
            $content = $this->normalizeContent(
                $referenceContent,
                self::getContents($file)
            );

            // TODO write content
        }
    }

    /**
     * @param \romanzipp\EnvNormalizer\Services\Content $referenceContent
     * @param \romanzipp\EnvNormalizer\Services\Content $originalContent
     *
     * @return \romanzipp\EnvNormalizer\Services\Content
     */
    public function normalizeContent(Content $referenceContent, Content $originalContent): Content
    {
        /**
         * @var string[]
         */
        $normalizedContent = [];

        $originalVariables = $originalContent->getVariables();
        $writtenVariables = [];

        foreach ($referenceContent->getLines() as $line) {
            // Just append the original line from the reference file if no value needs to be overriden
            if ( ! $line->isVariable()) {
                $normalizedContent[] = $line->getContent();
                continue;
            }

            // Variable exists in reference file but not in target file, just skip
            if ( ! $originalContent->hasVariable($line->getVariable())) {
                continue;
            }

            // Append the reference variable with the original value
            $normalizedContent[] = $originalContent->buildVariableLine($line);

            $writtenVariables[$line->getVariable()] = $line;
        }

        $missingVariables = array_diff(array_keys($originalVariables), array_keys($writtenVariables));

        if ( ! empty($missingVariables)) {
            $normalizedContent[] = '';
            $normalizedContent[] = '# Not found while normalizing';
            $normalizedContent[] = '';

            foreach ($missingVariables as $name) {
                $normalizedContent[] = $originalContent->buildVariableLine($originalVariables[$name]);
            }
        }

        return new Content(implode(PHP_EOL, $normalizedContent));
    }

    /**
     * @param \SplFileInfo $file
     *
     * @return \romanzipp\EnvNormalizer\Services\Content
     */
    public static function getContents(SplFileInfo $file): Content
    {
        return new Content(file_get_contents($file->getPathname()));
    }
}
