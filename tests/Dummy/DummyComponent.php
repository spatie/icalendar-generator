<?php

namespace Spatie\Calendar\Tests\Dummy;

use Spatie\Calendar\ComponentPayload;
use Spatie\Calendar\Components\Component;

class DummyComponent extends Component
{
    /** @var string */
    public $name;

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
            'name'
        ];
    }

    public function getPayload(): ComponentPayload
    {
        $this->ensureRequiredPropertiesAreSet();

        return ComponentPayload::new($this->getComponentType());
    }
}
