<?php

namespace Spatie\IcalendarGenerator\Properties;

final class UriProperty extends Property
{
    public static function create(string $name, string $uri): UriProperty
    {
        return new self($name, $uri);
    }

    public function __construct(string $name, protected string $uri)
    {
        $this->name = $name;
    }

    public function getValue(): string
    {
        $uri = filter_var($this->uri, FILTER_VALIDATE_URL);

        return $uri === false ? '' : $uri;
    }

    public function getOriginalValue(): string
    {
        return $this->uri;
    }
}
