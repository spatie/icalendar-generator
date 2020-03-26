<?php

namespace Spatie\IcalendarGenerator\PropertyTypes;

final class Parameter
{
    /** @var string */
    private $name;

    /** @var string */
    private $value;

    /** @var bool */
    private $disableEscaping;

    public static function create(string $name, string $value, $disableEscaping = false): Parameter
    {
        return new self($name, $value, $disableEscaping);
    }

    /**
     * Parameter constructor.
     *
     * @param string $name
     * @param string $value
     * @param bool $disableEscaping
     */
    public function __construct(string $name, string $value, $disableEscaping = false)
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
