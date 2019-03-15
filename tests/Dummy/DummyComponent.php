<?php

namespace Spatie\Calendar\Tests\Dummy;

use Spatie\Calendar\ComponentPayload;
use Spatie\Calendar\Components\Component;

class DummyComponent extends Component
{
    /** @var string */
    public $name;

    /** @var string */
    public $description;

    public function __construct(string $name)
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

    public function getPayload(): ComponentPayload
    {
        return ComponentPayload::new($this->getComponentType())
            ->textProperty('name', $this->name)
            ->textProperty('description', $this->description);
    }
}
