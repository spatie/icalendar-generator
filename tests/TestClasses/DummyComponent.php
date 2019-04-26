<?php

namespace Spatie\IcalendarGenerator\Tests\TestClasses;

use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Components\Component;

class DummyComponent extends Component
{
    /** @var array */
    public $subComponents = [];

    /** @var string */
    public $name;

    /** @var string */
    public $description;

    public function __construct(?string $name = null)
    {
        $this->name = $name;
    }

    public function subComponent($subComponent): DummyComponent
    {
        $this->subComponents[] = $subComponent;

        return $this;
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

    public function getPayload(): ComponentPayload
    {
        return ComponentPayload::create($this->getComponentType())
            ->textProperty('name', $this->name)
            ->textProperty('description', $this->description)
            ->subComponent(...$this->subComponents);
    }
}
