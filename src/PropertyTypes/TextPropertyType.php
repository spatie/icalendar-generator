<?php

namespace Spatie\IcalendarGenerator\PropertyTypes;

class TextPropertyType extends PropertyType
{
    private string $text;

    private bool $disableEscaping;

    public static function create(string $name, string $text, $disableEscaping = false): TextPropertyType
    {
        return new self($name, $text, $disableEscaping);
    }

    public function __construct(string $name, string $text, $disableEscaping = false)
    {
        $this->name = $name;
        $this->text = $text;
        $this->disableEscaping = $disableEscaping;
    }

    public function getValue(): string
    {
        if ($this->disableEscaping) {
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
