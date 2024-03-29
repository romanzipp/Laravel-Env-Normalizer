<?php

namespace romanzipp\EnvNormalizer\Services;

class NormalizerService
{
    private bool $createBackup = false;

    /**
     * Reference .env.example file.
     *
     * @var \SplFileInfo
     */
    private \SplFileInfo $reference;

    /**
     * File to write.
     *
     * @var \SplFileInfo[]
     */
    private array $targets = [];

    /**
     * @param string $referencePath
     * @param string[] $targetPaths
     */
    public function __construct(string $referencePath, array $targetPaths)
    {
        $this->reference = new \SplFileInfo($referencePath);

        foreach ($targetPaths as $targetPath) {
            $this->targets[] = new \SplFileInfo($targetPath);
        }

        $this->validate();
    }

    public function withBackup(bool $ignoreExisting = false): self
    {
        if ( ! $ignoreExisting) {
            foreach ($this->targets as $target) {
                if (file_exists($target->getPathname() . '.bak')) {
                    throw new \InvalidArgumentException(sprintf('Backup file "%s.bak" already exists', $target->getFilename()));
                }
            }
        }

        $this->createBackup = true;

        return $this;
    }

    private function validate(): void
    {
        if (empty($this->targets)) {
            throw new \InvalidArgumentException('No target specified');
        }

        foreach ([$this->reference, ...$this->targets] as $file) {
            /**
             * @var \SplFileInfo $file
             */
            if ( ! file_exists($file->getPathname())) {
                throw new \InvalidArgumentException('File not found');
            }

            if ( ! $file->isFile()) {
                throw new \InvalidArgumentException('Paths must be files');
            }
        }

        foreach ($this->targets as $target) {
            if ( ! $target->isWritable()) {
                throw new \InvalidArgumentException('Can not write output files');
            }
        }
    }

    /**
     * @return \romanzipp\EnvNormalizer\Services\Content[]
     */
    public function normalize(): array
    {
        $contents = [];

        $referenceContent = self::getContents($this->reference);

        foreach ($this->targets as $target) {
            if ($this->createBackup) {
                copy($target->getPathname(), $target->getPathname() . '.bak');
            }

            $contents[] = $content = $this->normalizeContent(
                $referenceContent,
                self::getContents($target)
            );

            file_put_contents($target->getPathname(), (string) $content);
        }

        return $contents;
    }

    /**
     * @return \romanzipp\EnvNormalizer\Services\Content[]
     */
    public function dry(): array
    {
        $contents = [];

        $referenceContent = self::getContents($this->reference);

        foreach ($this->targets as $target) {
            $contents[] = $this->normalizeContent(
                $referenceContent,
                self::getContents($target)
            );
        }

        return $contents;
    }

    /**
     * @param Content $referenceContent
     * @param Content $targetContent
     *
     * @return Content
     */
    public function normalizeContent(Content $referenceContent, Content $targetContent): Content
    {
        /**
         * @var array<int, string|null> $normalizedContent
         */
        $normalizedContent = [];

        $originalVariables = $targetContent->getVariables();
        $writtenVariables = [];

        foreach ($referenceContent->getLines() as $line) {
            // Just append the original line from the reference file if no value needs to be overriden
            if ( ! $line->isVariable()) {
                if ( ! $line->isComment()) {
                    $normalizedContent[] = $line->getContent();
                }

                continue;
            }

            // Variable exists in reference file but not in target file, just skip
            if ( ! $targetContent->hasVariable($line->getVariable())) {
                continue;
            }

            // Append the reference variable with the original value
            $normalizedContent[] = $targetContent->buildVariableLine($line);

            $writtenVariables[$line->getVariable()] = $line;
        }

        // Append comments (which are no headers) to the end

        $originalComments = [];

        foreach ($targetContent->getLines() as $line) {
            if ( ! $line->isComment()) {
                continue;
            }

            $originalComments[] = $line->getContent();
        }

        if ( ! empty($originalComments)) {
            $normalizedContent[] = '';
            $normalizedContent[] = '# Unset';
            $normalizedContent[] = '';

            foreach ($originalComments as $originalComment) {
                $normalizedContent[] = $originalComment;
            }
        }

        $missingVariables = array_diff(array_keys($originalVariables), array_keys($writtenVariables));

        if ( ! empty($missingVariables)) {
            $normalizedContent[] = '';
            $normalizedContent[] = '# Additional';
            $normalizedContent[] = '';

            foreach ($missingVariables as $name) {
                $normalizedContent[] = $targetContent->buildVariableLine($originalVariables[$name]);
            }
        }

        $normalizedContent = array_values($normalizedContent);

        // Remove comments without following variables
        foreach ($normalizedContent as $index => $line) {
            if ( ! isset($normalizedContent[$index])) {
                continue;
            }

            if ( ! Line::contentIsHeader($line)) {
                continue;
            }

            $found = false;
            $i = $index + 1;

            while (isset($normalizedContent[$i])) {
                $line = $normalizedContent[$i];

                if ( ! Line::contentIsBlank($line) && ! Line::contentIsHeader($line)) {
                    $found = true;
                }

                if ( ! $found && Line::contentIsHeader($line)) {
                    break;
                }

                $i = $i + 1;
            }

            if ( ! $found) {
                for ($j = $index; $j <= $i; ++$j) {
                    unset($normalizedContent[$j]);
                }
            }
        }

        $normalizedContent = array_values($normalizedContent);

        // Remove empty lines with followings counts of 2+
        foreach ($normalizedContent as $index => $line) {
            if (empty($line) && isset($normalizedContent[$index + 1]) && empty($normalizedContent[$index + 1])) {
                unset($normalizedContent[$index]);
            }
        }

        return new Content(implode(PHP_EOL, $normalizedContent), $targetContent->getTitle());
    }

    /**
     * @param \SplFileInfo $file
     *
     * @return Content
     */
    public static function getContents(\SplFileInfo $file): Content
    {
        return new Content(file_get_contents($file->getPathname()), $file->getFilename());
    }
}
