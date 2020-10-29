<?php

namespace Spatie\IcalendarGenerator\Properties;

final class UriProperty extends Property
{
    private string $uri;

    public static function create(string $name, string $uri): UriProperty
    {
        return new self($name, $uri);
    }

    public function __construct(string $name, string $uri)
    {
        $this->name = $name;
        $this->uri = $uri;
    }

    public function getValue(): string
    {
        return filter_var($this->uri, FILTER_VALIDATE_URL);
    }

    public function getOriginalValue(): string
    {
        return $this->uri;
    }
}
