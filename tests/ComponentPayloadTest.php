<?php

namespace Spatie\Calendar\Tests;

use DateTime;
use Exception;
use Spatie\Calendar\ComponentPayload;
use Spatie\Calendar\PropertyTypes\DateTimeProperty;
use Spatie\Calendar\PropertyTypes\TextProperty;
use Spatie\Calendar\Tests\Dummy\DummyComponent;

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
    public function a_payload_can_give_a_specified_property()
    {
        $payload = (new ComponentPayload('TESTCOMPONENT'))
            ->textProperty('text', 'Some text here');

        $property = $payload->getProperty('text');

        $this->assertEquals('Some text here', $property->getOriginalValue());
    }

    /** @test */
    public function an_exception_will_be_thrown_when_an_property_does_not_exist()
    {
        $this->expectException(Exception::class);

        $payload = (new ComponentPayload('TESTCOMPONENT'));

        $payload->getProperty('text');
    }
}
