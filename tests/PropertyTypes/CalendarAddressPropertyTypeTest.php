<?php

namespace Spatie\IcalendarGenerator\Tests\PropertyTypes;

use Spatie\IcalendarGenerator\Enums\ParticipationStatus;
use Spatie\IcalendarGenerator\PropertyTypes\CalendarAddressPropertyType;
use Spatie\IcalendarGenerator\Tests\TestCase;
use Spatie\IcalendarGenerator\ValueObjects\CalendarAddress;

class CalendarAddressPropertyTypeTest extends TestCase
{
    /** @test */
    public function it_can_create_a_calendar_property_type()
    {
        $property = new CalendarAddressPropertyType(
            'ORGANIZER',
            new CalendarAddress('ruben@spatie.be')
        );

        $this->assertEquals(['ORGANIZER'], $property->getNames());
        $this->assertEquals('MAILTO:ruben@spatie.be', $property->getValue());

        $this->assertParameterCountInProperty(0, $property);
    }

    /** @test */
    public function it_can_set_a_name_and_participation_status()
    {
        $property = new CalendarAddressPropertyType(
            'ORGANIZER',
            new CalendarAddress('ruben@spatie.be', 'Ruben', ParticipationStatus::accepted())
        );

        $this->assertEquals(['ORGANIZER'], $property->getNames());
        $this->assertEquals('MAILTO:ruben@spatie.be', $property->getValue());

        $this->assertParameterCountInProperty(2, $property);
        $this->assertParameterEqualsInProperty('CN', 'Ruben', $property);
        $this->assertParameterEqualsInProperty('PARTSTAT', ParticipationStatus::accepted()->value, $property);
    }
}
