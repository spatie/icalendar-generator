<?php

namespace Spatie\Calendar\Tests;

use DateTime;
use PHPUnit\Framework\TestCase;
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
    public function a_payload_includes_subcomponents()
    {
        $subcomponents = [
            new DummyComponent('subcomponent1'),
            new DummyComponent('subcomponent2'),
        ];

        $payload = (new ComponentPayload('TESTCOMPONENT'))
            ->subComponent(...$subcomponents);

        $this->assertEquals($subcomponents, $payload->getSubComponents());
    }
}
