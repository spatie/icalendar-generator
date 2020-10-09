<?php

namespace Spatie\IcalendarGenerator\PropertyTypes;

final class UriPropertyType extends PropertyType
{
    /** @var string */
    private $uri;

    public static function create($names, string $uri): UriPropertyType
    {
        return new self($names, $uri);
    }

    /**
     * UriPropertyType constructor.
     *
     * @param array|string $names
     * @param string $uri
     */
    public function __construct($names, string $uri)
    {
        parent::__construct($names);

        $this->uri = $uri;
    }

    public function getValue(): string
    {
        return $this->uri;
    }

    public function getOriginalValue(): string
    {
        return $this->uri;
    }
}
