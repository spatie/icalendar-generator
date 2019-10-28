<?php

namespace Spatie\IcalendarGenerator\PropertyTypes;

final class Parameter
{
    /** @var string */
    private $name;

    /** @var string */
    private $value;

    public static function create(string $name, string $value): Parameter
    {
        return new self($name, $value);
    }

    public function __construct(string $name, string $value)
    {
        $this->name = $name;

        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
