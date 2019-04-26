<?php

namespace Spatie\IcalendarGenerator\Tests;

use DateTime;
use Exception;
use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\PropertyTypes\Parameter;
use Spatie\IcalendarGenerator\Tests\TestClasses\DummyComponent;
use Spatie\IcalendarGenerator\Tests\TestClasses\DummyPropertyType;
use Spatie\IcalendarGenerator\PropertyTypes\TextPropertyType;
use Spatie\IcalendarGenerator\PropertyTypes\DateTimePropertyType;

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
            ->textProperty('text', 'Some text here')
            ->dateTimeProperty('date', $date);

        $this->assertEquals([
            new TextPropertyType('text', 'Some text here'),
            new DateTimePropertyType('date', $date),
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
    public function a_payload_can_give_a_specified_property()
    {
        $payload = (new ComponentPayload('TESTCOMPONENT'))
            ->textProperty('text', 'Some text here');

        $this->assertPropertyEqualsInPayload('text', 'Some text here', $payload);
    }

    /** @test */
    public function an_exception_will_be_thrown_when_an_property_does_not_exist()
    {
        $this->expectException(Exception::class);

        $payload = (new ComponentPayload('TESTCOMPONENT'));

        $payload->getProperty('text');
    }

    /** @test */
    public function a_when_will_only_be_executed_when_the_condition_is_true()
    {
        $payload = (new ComponentPayload('TESTCOMPONENT'));

        $payload->when(false, function (ComponentPayload $componentPayload) {
            $componentPayload->textProperty('text', 'Some text here');
        });

        $payload->when(true, function (ComponentPayload $componentPayload) {
            $componentPayload->textProperty('text', 'Other text here');
        });

        $this->assertPropertyEqualsInPayload('text', 'Other text here', $payload);
    }

    /** @test */
    public function a_property_can_be_added_with_parameters()
    {
        $property = new DummyPropertyType('name', 'TESTPROPERTY');

        $parameters = [
            new Parameter('hello', 'world'),
        ];

        $payload = (new ComponentPayload('TESTCOMPONENT'))
            ->property($property, $parameters);

        $this->assertEquals($parameters, $payload->getProperty('name')->getParameters());
    }

    /** @test */
    public function a_property_can_be_aliased()
    {
        $payload = (new ComponentPayload('TESTCOMPONENT'))
            ->textProperty('alpha', 'Some text here')
            ->alias('alpha', ['beta']);

        $this->assertEquals(['beta'], $payload->getAliasesForProperty('alpha'));
    }
}
