<?php

namespace Spatie\IcalendarGenerator\Properties;

use BackedEnum;

class TextProperty extends Property
{
    public static function create(string $name, string $text): TextProperty
    {
        return new self($name, $text);
    }

    public static function createFromEnum(string $name, BackedEnum $enum): TextProperty
    {
        return new self($name, (string) $enum->value);
    }

    public function __construct(
        protected string $name,
        protected string $text,
        protected bool $escaped = true,
    ) {
    }

    public function withoutEscaping(): self
    {
        $this->escaped = false;

        return $this;
    }

    public function getValue(): string
    {
        if ($this->escaped === false) {
            return $this->text;
        }

        $replacements = [
            '\\' => '\\\\',
            '"' => '\\"',
            ',' => '\\,',
            ';' => '\\;',
            "\n" => '\\n',
        ];

        return str_replace(array_keys($replacements), $replacements, $this->text);
    }

    public function getOriginalValue(): string
    {
        return $this->text;
    }
}
