<?php

namespace Spatie\Calendar\Tests\Calendar;

use Exception;
use Spatie\Calendar\Tests\Dummy\DummyComponent;
use Spatie\Calendar\Tests\Dummy\DummyPropertyType;
use Spatie\Calendar\Tests\TestCase;

class HasSubComponentsTest extends TestCase
{
    /** @test */
    public function it_can_add_an_sub_component()
    {
        $component = new DummyComponent('DAFT');

        $component->subComponent(new DummyComponent('PUNK'));

        $payload = $component->getPayload();

        $this->assertEquals([
            new DummyComponent('PUNK'),
        ], $payload->getSubComponents());
    }

    /** @test */
    public function it_can_add_sub_components_by_array()
    {
        $component = new DummyComponent('JOHN');

        $subComponent = [
            new DummyComponent('RINGO'),
            new DummyComponent('PAUL'),
            new DummyComponent('GEORGE'),
        ];

        $component->subComponent($subComponent);

        $payload = $component->getPayload();

        $this->assertEquals($subComponent, $payload->getSubComponents());
    }

    /** @test */
    public function it_can_add_sub_components_by_closure()
    {
        $component = new DummyComponent('SIMON');

        $component->subComponent(function (DummyComponent $dummyComponent) {
            $dummyComponent->name = 'GARFUNKEL';
        });

        $payload = $component->getPayload();

        $this->assertEquals([
            new DummyComponent('GARFUNKEL'),
        ], $payload->getSubComponents());
    }

    /** @test */
    public function it_can_only_use_components_in_a_closure()
    {
        $this->expectException(Exception::class);

        $component = new DummyComponent('U2');

        $component->subComponent(function (DummyPropertyType $propertyType) {
        });
    }
}
