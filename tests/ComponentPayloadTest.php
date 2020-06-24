<?php

namespace Spatie\IcalendarGenerator\Tests;

use DateTime;
use Exception;
use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Properties\DateTimeProperty;
use Spatie\IcalendarGenerator\Properties\Parameter;
use Spatie\IcalendarGenerator\Properties\TextProperty;
use Spatie\IcalendarGenerator\Tests\TestClasses\DummyComponent;
use Spatie\IcalendarGenerator\Tests\TestClasses\DummyProperty;

class ComponentPayloadTest extends TestCase
{
    /** @test */
    public function a_payload_has_a_type()
    {
        $payload = (new ComponentPayload('TESTCOMPONENT'));

        $this->assertEquals('TESTCOMPONENT', $payload->getType());
    }

    /** @test */
    public function a_payload_includes_properties()
    {
        $date = new DateTime();

        $payload = (new ComponentPayload('TESTCOMPONENT'))
            ->property(TextProperty::create('text', 'Some text here'))
            ->property(DateTimeProperty::create('date', $date));

        $this->assertEquals([
            new TextProperty('text', 'Some text here'),
            new DateTimeProperty('date', $date),
        ], $payload->getProperties());
    }

    /** @test */
    public function a_payload_includes_sub_components()
    {
        $subComponents = [
            new DummyComponent('subComponent1'),
            new DummyComponent('subComponent1'),
        ];

        $payload = (new ComponentPayload('TESTCOMPONENT'))
            ->subComponent(...$subComponents);

        $this->assertEquals($subComponents, $payload->getSubComponents());
    }

    /** @test */
    public function an_exception_will_be_thrown_when_an_property_does_not_exist()
    {
        $this->expectException(Exception::class);

        $payload = (new ComponentPayload('TESTCOMPONENT'));

        $payload->getProperty('text');
    }

    /** @test */
    public function an_optional_will_only_be_added_when_the_condition_is_true()
    {
        $payload = (new ComponentPayload('TESTCOMPONENT'));

        $payload->optional(false, fn () => TextProperty::create('text', 'Some text here'));
        $payload->optional(true, fn () => TextProperty::create('text', 'Other text here'));

        $this->assertPropertyEqualsInPayload('text', 'Other text here', $payload);
    }

    /** @test */
    public function an_optional_will_only_be_added_when_it_has_a_value()
    {
        $payload = (new ComponentPayload('TESTCOMPONENT'));

        $payload->optional(null, fn () => TextProperty::create('text', 'Some text here'));
        $payload->optional('something', fn () => TextProperty::create('text', 'Other text here'));

        $this->assertPropertyEqualsInPayload('text', 'Other text here', $payload);
    }

    /** @test */
    public function an_multiple_will_be_added_via_closure()
    {
        $payload = (new ComponentPayload('TESTCOMPONENT'));

        $payload->multiple(['a', 'b', 'c'], fn (string $letter) => TextProperty::create('text', $letter));

        $this->assertEquals([
            TextProperty::create('text', 'a'),
            TextProperty::create('text', 'b'),
            TextProperty::create('text', 'c'),
        ], $payload->getProperties());
    }

    /** @test */
    public function a_property_can_be_added_with_parameters()
    {
        $property = new DummyProperty('name', 'TESTPROPERTY');

        $parameters = [
            new Parameter('hello', 'world'),
        ];

        $payload = (new ComponentPayload('TESTCOMPONENT'))
            ->property($property, $parameters);

        $this->assertEquals($parameters, $payload->getProperty('name')->getParameters());
    }
}
