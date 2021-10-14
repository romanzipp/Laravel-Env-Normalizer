<?php

namespace romanzipp\EnvNormalizer\Services;

use Illuminate\Support\Str;

class Line
{
    private string $content;

    private ?string $variable;

    private ?string $value;

    public function __construct(string $content)
    {
        $this->content = trim($content);
        $this->variable = self::getContentVariable($this->content);
        $this->value = self::getContentValue($this->content);
    }

    public function isBlank(): bool
    {
        return '' === $this->content;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function isHeader(): bool
    {
        return self::contentIsHeader($this->content);
    }

    public function isVariable(): bool
    {
        if ($this->isBlank() || $this->isHeader()) {
            return false;
        }

        return null !== $this->getVariable();
    }

    public function getVariable(): ?string
    {
        return $this->variable;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public static function getContentValue(string $content): ?string
    {
        if (preg_match('/^[A-Z0-9_]+=(.*)/', $content, $matches)) {
            return $matches[1] ?: null;
        }

        return null;
    }

    public static function getContentVariable(string $content): ?string
    {
        if (preg_match('/^([A-Z0-9_]+)=/', $content, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public static function contentIsHeader(string $content): bool
    {
        return Str::startsWith($content, '# ');
    }

    public static function contentIsBlank(string $content): bool
    {
        return '' === trim($content);
    }
}
