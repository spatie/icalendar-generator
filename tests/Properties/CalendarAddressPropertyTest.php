<?php

namespace Spatie\IcalendarGenerator\Tests\Properties;

use Spatie\IcalendarGenerator\Enums\ParticipationStatus;
use Spatie\IcalendarGenerator\Properties\CalendarAddressProperty;
use Spatie\IcalendarGenerator\Tests\PropertyExpectation;
use Spatie\IcalendarGenerator\Tests\TestCase;
use Spatie\IcalendarGenerator\ValueObjects\CalendarAddress;

class CalendarAddressPropertyTest extends TestCase
{
    /** @test */
    public function it_can_create_a_calendar_property_type()
    {
        $property = new CalendarAddressProperty(
            'ORGANIZER',
            new CalendarAddress('ruben@spatie.be')
        );

        PropertyExpectation::create($property)
            ->expectName('ORGANIZER')
            ->expectOutput('MAILTO:ruben@spatie.be')
            ->expectParameterCount(0);
    }

    /** @test */
    public function it_can_set_a_name_and_participation_status()
    {
        $property = new CalendarAddressProperty(
            'ORGANIZER',
            new CalendarAddress('ruben@spatie.be', 'Ruben', ParticipationStatus::accepted())
        );

        PropertyExpectation::create($property)
            ->expectName('ORGANIZER')
            ->expectOutput('MAILTO:ruben@spatie.be')
            ->expectParameterCount(2)
            ->expectParameterValue('CN', 'Ruben')
            ->expectParameterValue('PARTSTAT', ParticipationStatus::accepted()->value);
    }

     /** @test */
     public function it_can_set_rsvp_to_true()
    {
        $property = new CalendarAddressProperty(
            'ATTENDEE',
            new CalendarAddress('ruben@spatie.be', 'Ruben', ParticipationStatus::needs_action(), true)
        );

        PropertyExpectation::create($property)
            ->expectName('ATTENDEE')
            ->expectOutput('MAILTO:ruben@spatie.be')
            ->expectParameterCount(3)
            ->expectParameterValue('CN', 'Ruben')
            ->expectParameterValue('RSVP', 'TRUE')
            ->expectParameterValue('PARTSTAT', ParticipationStatus::needs_action()->value);
    }
}
