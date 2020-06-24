<?php

namespace Spatie\IcalendarGenerator\Properties;

use Spatie\Enum\Enum;

class Parameter
{
    private string $name;

    private string $value;

    private bool $disableEscaping;

    public static function create(string $name, $value, $disableEscaping = false): Parameter
    {
        return new self($name, $value, $disableEscaping);
    }

    public function __construct(string $name, $value, $disableEscaping = false)
    {
        $this->name = $name;
        $this->value = $value instanceof Enum ? $value->value : $value;
        $this->disableEscaping = $disableEscaping;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        if ($this->disableEscaping) {
            return $this->value;
        }

        $replacements = [
            '\\' => '\\\\',
            '"' => '\\"',
            ',' => '\\,',
            ';' => '\\;',
            "\n" => '\\n',
        ];

        return str_replace(array_keys($replacements), $replacements, $this->value);
    }
}
