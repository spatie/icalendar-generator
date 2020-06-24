<?php

namespace Spatie\IcalendarGenerator\Tests\TestClasses;

use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Components\Component;

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
        return 'DUMMY';
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
            ->textProperty('name', $this->name)
            ->textProperty('description', $this->description);
    }
}
