<?php

namespace Spatie\Calendar\Tests\Dummy;

use Spatie\Calendar\ComponentPayload;
use Spatie\Calendar\Components\Component;
use Spatie\Calendar\HasSubComponents;

class DummyComponent extends Component
{
    use HasSubComponents;

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
        $this->addSubComponent($subComponent);

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
        return ComponentPayload::new($this->getComponentType())
            ->textProperty('name', $this->name)
            ->textProperty('description', $this->description)
            ->subComponent(...$this->subComponents);
    }
}
