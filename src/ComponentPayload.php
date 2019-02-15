<?php

namespace Spatie\Calendar;

use DateTime;
use Spatie\Calendar\Components\Component;

class ComponentPayload
{
    /** @var string */
    protected $identifier;

    /** @var array */
    protected $properties = [];

    /** @var array */
    protected $components = [];

    public static function new(string $identifier): ComponentPayload
    {
        return new self($identifier);
    }

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    public function add(string $key, $item): ComponentPayload
    {
        if ($this instanceof DateTime) {
            return $this->addDateTime($key, $item);
        }

        $this->properties[$key] = $item;

        return $this;
    }

    public function addDateTime(string $key, DateTime $item): ComponentPayload
    {
        $this->properties[$key] = $item->format('Ymd\THis');

        return $this;
    }

    public function addComponent(Component $component): ComponentPayload
    {
        $this->components[] = $component;

        return $this;
    }

    public function addComponents(array $components): ComponentPayload
    {
        foreach ($components as $component) {
            $this->addComponent($component);
        }

        return $this;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getComponents(): array
    {
        return $this->components;
    }
}
