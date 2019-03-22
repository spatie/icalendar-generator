<?php

namespace Spatie\Calendar\Tests\Components;

use Spatie\Calendar\Exceptions\PropertyIsRequired;
use Spatie\Calendar\PropertyTypes\TextPropertyType;
use Spatie\Calendar\Tests\Dummy\DummyComponent;
use Spatie\Calendar\Tests\TestCase;

class ComponentTest extends TestCase {

    /** @test */
    public function it_will_check_if_all_required_properties_are_set()
    {
        $dummy = new DummyComponent('Dummy');

        $payloadString =  $dummy->toString();

        $this->assertNotNull($payloadString);
    }

    /** @test */
    public function it_will_throw_an_exception_when_a_required_property_is_not_set()
    {
        $this->expectException(PropertyIsRequired::class);

        $dummy = new DummyComponent('Dummy');

        $dummy->name = null;

        $dummy->toString();
    }

    /** @test */
    public function it_will_throw_an_exception_when_a_required_property_is_not_set_but_another_is()
    {
        $this->expectException(PropertyIsRequired::class);

        $dummy = new DummyComponent('Dummy');

        $dummy->name = null;
        $dummy->description = 'Hello there';

        $dummy->toString();
    }
}
