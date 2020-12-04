<?php

namespace Spatie\IcalendarGenerator\Properties;

use Spatie\Enum\Enum;

class TextProperty extends Property
{
    private string $text;

    private bool $escaped = true;

    public static function create(string $name, string $text): TextProperty
    {
        return new self($name, $text);
    }

    public static function createFromEnum(string $name, Enum $enum): TextProperty
    {
        return new self($name, (string) $enum->value);
    }

    public function __construct(string $name, string $text)
    {
        $this->name = $name;
        $this->text = $text;
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
