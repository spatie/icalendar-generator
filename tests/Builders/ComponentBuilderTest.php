<?php

namespace Spatie\Calendar\Tests\Builders;

use PHPUnit\Framework\TestCase;
use Spatie\Calendar\Builders\ComponentBuilder;
use Spatie\Calendar\ComponentPayload;
use Spatie\Calendar\Tests\Dummy\DummyComponent;
use Spatie\Calendar\Tests\Dummy\DummyProperty;

class ComponentBuilderTest extends TestCase
{
    /** @test */
    public function it_can_build_a_component_payload_with_properties()
    {
        $payload = ComponentPayload::new('TEST');

        $payload->property(new DummyProperty('location', 'Antwerp'));

        $builder = new ComponentBuilder($payload);

        $this->assertEquals(
            'BEGIN:VTEST\r\nlocation:Antwerp\r\nEND:VTEST',
            $builder->build()
        );
    }

    /** @test */
    public function it_can_build_a_component_payload_with_subcomponents()
    {
        $payload = ComponentPayload::new('TEST');

        $payload->subComponent(new DummyComponent('SUBCOMPONENT'));

        $builder = new ComponentBuilder($payload);

        $this->assertEquals(
            'BEGIN:VTEST\r\nBEGIN:VDUMMY\r\nEND:VDUMMY\r\nEND:VTEST',
            $builder->build()
        );
    }

    /** @test */
    public function it_will_chip_a_line_when_it_becomes_too_long()
    {
        $payload = ComponentPayload::new('TEST');

        $payload->property(new DummyProperty('location', 'This is a really long text. Possibly you will never write a text like this in a property. But hey we support the RFC so let us chip it! You can maybe write some HTML in here that will make it longer than usual.'));

        $builder = new ComponentBuilder($payload);

        $this->assertEquals(
            'BEGIN:VTEST\r\nlocation:This is a really long text. Possibly you will never write a text l\r\n ike this in a property. But hey we support the RFC so let us chip it! You \r\n can maybe write some HTML in here that will make it longer than usual.\r\nEND:VTEST',
            $builder->build()
        );
    }
}
