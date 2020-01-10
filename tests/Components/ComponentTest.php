<?php

namespace Spatie\IcalendarGenerator\Tests\Components;

use DateTime;
use Spatie\IcalendarGenerator\Components\Alert;
use Spatie\IcalendarGenerator\Exceptions\InvalidComponent;
use Spatie\IcalendarGenerator\PropertyTypes\TextPropertyType;
use Spatie\IcalendarGenerator\Tests\TestCase;
use Spatie\IcalendarGenerator\Tests\TestClasses\DummyComponent;

class ComponentTest extends TestCase
{
    /** @test */
    public function it_will_check_if_all_required_properties_are_set()
    {
        $dummy = new DummyComponent('Dummy');

        $payloadString = $dummy->toString();

        $this->assertNotNull($payloadString);
    }

    /** @test */
    public function it_will_throw_an_exception_when_a_required_property_is_not_set()
    {
        $this->expectException(InvalidComponent::class);

        $dummy = new DummyComponent('Dummy');

        $dummy->name = null;

        $dummy->toString();
    }

    /** @test */
    public function it_will_throw_an_exception_when_a_required_property_is_not_set_but_another_is()
    {
        $this->expectException(InvalidComponent::class);

        $dummy = new DummyComponent('Dummy');

        $dummy->name = null;
        $dummy->description = 'Hello there';

        $dummy->toString();
    }

    /** @test */
    public function it_can_add_an_extra_property()
    {
        $dummy = new DummyComponent('Dummy');

        $dummy->appendProperty(
            TextPropertyType::create('organizer', 'ruben@spatie.be')
        );

        $this->assertPropertyEqualsInPayload(
            'organizer',
            'ruben@spatie.be',
            $dummy->resolvePayload()
        );
    }

    /** @test */
    public function it_can_add_an_extra_sub_component()
    {
        $dummy = new DummyComponent('Dummy');

        $component = new Alert();

        $dummy->appendSubComponent($component);

        $this->assertCount(1, $dummy->resolvePayload()->getSubComponents());
        $this->assertEquals(
            $component,
            $dummy->resolvePayload()->getSubComponents()[0]
        );
    }
}
