<?php

namespace Spatie\IcalendarGenerator\Properties;

use BackedEnum;
use Spatie\IcalendarGenerator\ValueObjects\DateTimeValue;
use Stringable;

class Parameter
{
    public static function create(string $name, string|int|bool|BackedEnum|DateTimeValue|Stringable $value, bool $disableEscaping = false): Parameter
    {
        return new self($name, $value, $disableEscaping);
    }

    public function __construct(
        protected string $name,
        protected string|int|bool|BackedEnum|DateTimeValue|Stringable $value,
        protected bool $disableEscaping = false
    ) {
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
            // RFC 5545
            '\\' => '\\\\',
            ',' => '\\,',
            ';' => '\\;',
            // RFC 6868
            '^' => '^^',
            '"' => '^\'',
            PHP_EOL => '^n',
        ];

        return str_replace(array_keys($replacements), $replacements, $value);
    }

    protected function valueToString(): string
    {
        if (is_bool($this->value)) {
            $bool = $this->value ? 'TRUE' : 'FALSE';

            return "BOOLEAN:{$bool}";
        }

        if ($this->value instanceof BackedEnum) {
            return (string) $this->value->value;
        }

        if ($this->value instanceof DateTimeValue) {
            return $this->value->hasTime()
                ? "DATE-TIME:{$this->value->format()}"
                : "DATE:{$this->value->format()}";
        }

        return (string) $this->value;
    }
}
