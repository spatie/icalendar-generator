<?php

namespace Spatie\IcalendarGenerator\Tests\TestClasses;

use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Components\Component;
use Spatie\IcalendarGenerator\Properties\TextProperty;

class DummyComponent extends Component
{
    public ?string $name;

    public ?string $description = null;

    public function __construct(?string $name = null)
    {
        $this->name = $name;
    }

    public function getComponentType(): string
    {
        return 'VDUMMY';
    }

    public function getRequiredProperties(): array
    {
        return [
            'name',
        ];
    }

    protected function payload(): ComponentPayload
    {
        $payload = ComponentPayload::create($this->getComponentType());

        if ($this->name) {
            $payload->property(TextProperty::create('name', $this->name));
        }

        if ($this->description) {
            $payload->property(TextProperty::create('description', $this->description));
        }

        return $payload;
    }
}
