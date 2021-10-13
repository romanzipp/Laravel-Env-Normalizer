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
        $this->variable = $this->checkVariable();
        $this->value = $this->checkValue();
    }

    public function isBlank(): bool
    {
        return '' === $this->content;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function isComment(): bool
    {
        return self::contentIsComment($this->content);
    }

    public function isVariable(): bool
    {
        if ($this->isBlank() || $this->isComment()) {
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

    private function checkVariable(): ?string
    {
        if (preg_match('/^([A-Z0-9_]+)=?/', $this->content, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function checkValue(): ?string
    {
        if (preg_match('/^[A-Z0-9_]+=(.*)/', $this->content, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public static function contentIsComment(string $content): bool
    {
        return Str::startsWith($content, '#');
    }

    public static function contentIsBlank(string $content): bool
    {
        return '' === trim($content);
    }
}
