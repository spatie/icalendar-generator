<?php

namespace Spatie\IcalendarGenerator\PropertyTypes;

use Spatie\Enum\Enum;

final class Parameter
{
    /** @var string */
    private $name;

    /** @var string|\Spatie\Enum\Enum */
    private $value;

    /** @var bool */
    private $disableEscaping;

    public static function create(string $name, $value, $disableEscaping = false): Parameter
    {
        return new self($name, $value, $disableEscaping);
    }

    /**
     * Parameter constructor.
     *
     * @param string $name
     * @param string|\Spatie\Enum\Enum $value
     * @param bool $disableEscaping
     */
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
