<?php

namespace romanzipp\EnvNormalizer\Services;

class Content
{
    private string $content;

    /**
     * @var array|\romanzipp\EnvNormalizer\Services\Line[]
     */
    private array $lines;

    /**
     * @var array<string, \romanzipp\EnvNormalizer\Services\Line>
     */
    private array $variables;

    private ?string $title;

    public function __construct(string $content, ?string $title = null)
    {
        $this->content = trim($content);
        $this->title = $title;
        $this->variables = [];

        $this->lines = array_map(
            fn (string $lineContent) => new Line($lineContent),
            explode(PHP_EOL, $this->content)
        );

        foreach ($this->lines as $line) {
            if ($variableName = $line->getVariable()) {
                $this->variables[$variableName] = $line;
            }
        }
    }

    /**
     * @return array<string, \romanzipp\EnvNormalizer\Services\Line>
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    public function getVariable(string $name): ?Line
    {
        return $this->variables[$name] ?? null;
    }

    public function hasVariable(string $name): bool
    {
        return null !== $this->getVariable($name);
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Get the parsed and normalized line for a reference variable line.
     *
     * @param Line $referenceLine
     *
     * @return string
     */
    public function buildVariableLine(Line $referenceLine): string
    {
        $variableLine = $this->getVariable($referenceLine->getVariable());

        if (null === $variableLine) {
            return $referenceLine->getContent();
        }

        if (empty($variableLine->getValue())) {
            return $referenceLine->getVariable() . '=';
        }

        return $referenceLine->getVariable() . '=' . $variableLine->getValue();
    }

    /**
     * @return \romanzipp\EnvNormalizer\Services\Line[]
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    public function __toString(): string
    {
        return $this->content;
    }
}
