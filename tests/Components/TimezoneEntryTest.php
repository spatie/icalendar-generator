<?php

namespace Spatie\IcalendarGenerator\Tests\Components;

use DateTime;
use Spatie\IcalendarGenerator\Builders\ComponentBuilder;
use Spatie\IcalendarGenerator\Components\TimezoneEntry;
use Spatie\IcalendarGenerator\Enums\RecurrenceFrequency;
use Spatie\IcalendarGenerator\Enums\TimezoneEntryType;
use Spatie\IcalendarGenerator\Tests\PayloadExpectation;
use Spatie\IcalendarGenerator\Tests\TestCase;
use Spatie\IcalendarGenerator\ValueObjects\RRule;

class TimezoneEntryTest extends TestCase
{
    /** @test */
    public function it_can_create_a_standard_entry()
    {
        $payload = TimezoneEntry::create(
            TimezoneEntryType::standard(),
            new DateTime('16 may 2020 12:00:00'),
            '+00:00',
            '+02:00'
        )->resolvePayload();

        PayloadExpectation::create($payload)
            ->expectType('STANDARD')
            ->expectPropertyValue('DTSTART', new DateTime('16 may 2020 12:00:00'))
            ->expectPropertyValue('TZOFFSETFROM', '+00:00')
            ->expectPropertyValue('TZOFFSETTO', '+02:00');
    }

    /** @test */
    public function it_can_create_a_standard_entry_with_negative_offsets()
    {
        $payload = TimezoneEntry::create(
            TimezoneEntryType::standard(),
            new DateTime('16 may 2020 12:00:00'),
            '-00:00',
            '-02:00'
        )->resolvePayload();

        PayloadExpectation::create($payload)
            ->expectType('STANDARD')
            ->expectPropertyValue('DTSTART', new DateTime('16 may 2020 12:00:00'))
            ->expectPropertyValue('TZOFFSETFROM', '-00:00')
            ->expectPropertyValue('TZOFFSETTO', '-02:00');
    }

    /** @test */
    public function it_can_create_a_daylight_entry()
    {
        $payload = TimezoneEntry::create(
            TimezoneEntryType::daylight(),
            new DateTime('16 may 2020 12:00:00'),
            '+00:00',
            '+02:00'
        )->resolvePayload();

        PayloadExpectation::create($payload)
            ->expectType('DAYLIGHT')
            ->expectPropertyValue('DTSTART', new DateTime('16 may 2020 12:00:00'))
            ->expectPropertyValue('TZOFFSETFROM', '+00:00')
            ->expectPropertyValue('TZOFFSETTO', '+02:00');
    }

    /** @test */
    public function it_can_set_a_name_and_description()
    {
        $payload = TimezoneEntry::create(
            TimezoneEntryType::standard(),
            new DateTime('16 may 2020 12:00:00'),
            '+00:00',
            '+02:00'
        )
            ->name('Europe - Brussels')
            ->description('Belgian timezones ftw!')
            ->resolvePayload();

        PayloadExpectation::create($payload)
            ->expectPropertyValue('TZNAME', 'Europe - Brussels')
            ->expectPropertyValue('COMMENT', 'Belgian timezones ftw!');
    }

    /** @test */
    public function it_can_set_a_rrule()
    {
        $payload = TimezoneEntry::create(
            TimezoneEntryType::standard(),
            new DateTime('16 may 2020 12:00:00'),
            '+00:00',
            '+02:00'
        )
            ->rrule(RRule::frequency(RecurrenceFrequency::daily()))
            ->resolvePayload();

        PayloadExpectation::create($payload)
            ->expectPropertyValue('RRULE', RRule::frequency(RecurrenceFrequency::daily()));
    }

    /** @test */
    public function it_can_write_out_a_timezone_entry()
    {
        $payload = TimezoneEntry::create(
            TimezoneEntryType::daylight(),
            new DateTime('16 may 2020 12:00:00'),
            '+00:00',
            '+02:00'
        )
            ->rrule(RRule::frequency(RecurrenceFrequency::daily()))
            ->name('Europe - Brussels')
            ->description('Belgian timezones ftw!')
            ->resolvePayload();

        $written = ComponentBuilder::create($payload)->build();

        $this->assertMatchesSnapshot($written);
    }
}
