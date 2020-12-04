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
        return ComponentPayload::create($this->getComponentType())
            ->optional($this->name, fn () => TextProperty::create('name', $this->name))
            ->optional($this->description, fn () => TextProperty::create('description', $this->description));
    }
}
