<?php

namespace Spatie\IcalendarGenerator\Properties;

use Spatie\Enum\Enum;
use Spatie\IcalendarGenerator\ValueObjects\DateTimeValue;

class Parameter
{
    private string $name;

    /** @var mixed|\Spatie\Enum\Enum|\Spatie\IcalendarGenerator\ValueObjects\DateTimeValue */
    private $value;

    private bool $disableEscaping;

    public static function create(string $name, $value, $disableEscaping = false): Parameter
    {
        return new self($name, $value, $disableEscaping);
    }

    public function __construct(string $name, $value, $disableEscaping = false)
    {
        $this->name = $name;
        $this->value = $value;
        $this->disableEscaping = $disableEscaping;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        $value = $this->valueToString();

        if ($this->disableEscaping) {
            return $value;
        }

        $replacements = [
            '\\' => '\\\\',
            '"' => '\\"',
            ',' => '\\,',
            ';' => '\\;',
            "\n" => '\\n',
        ];

        return str_replace(array_keys($replacements), $replacements, $value);
    }

    private function valueToString(): string
    {
        if (is_bool($this->value)) {
            $bool = $this->value ? 'TRUE' : 'FALSE';

            return "BOOLEAN:{$bool}";
        }

        if ($this->value instanceof Enum) {
            return (string) $this->value->value;
        }

        if ($this->value instanceof DateTimeValue) {
            return $this->value->hasTime()
                ? "DATE-TIME:{$this->value->format()}"
                : "DATE:{$this->value->format()}";
        }

        return $this->value;
    }
}
