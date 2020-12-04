<?php

namespace Spatie\IcalendarGenerator\Tests\Components;

use DateTime;
use DateTimeZone;
use Spatie\IcalendarGenerator\Builders\ComponentBuilder;
use Spatie\IcalendarGenerator\Components\Timezone;
use Spatie\IcalendarGenerator\Components\TimezoneEntry;
use Spatie\IcalendarGenerator\Enums\TimezoneEntryType;
use Spatie\IcalendarGenerator\Tests\TestCase;

class TimezoneTest extends TestCase
{
    /** @test */
    public function it_can_create_a_timezone()
    {
        $payload = Timezone::create('Europe/Brussels')->resolvePayload();

        $this->assertPropertyEqualsInPayload('TZID', 'Europe/Brussels', $payload);
    }

    /** @test */
    public function it_can_set_a_last_modified_date_as_utc()
    {
        $payload = Timezone::create('Europe/Brussels')
            ->lastModified(new DateTime('16 may 2020 12:00:00', new DateTimeZone('Europe/Brussels')))
            ->resolvePayload();

        $this->assertPropertyEqualsInPayload(
            'LAST-MODIFIED',
            new DateTime('16 may 2020 10:00:00', new DateTimeZone('UTC')),
            $payload
        );
    }

    /** @test */
    public function it_can_set_an_url()
    {
        $payload = Timezone::create('Europe/Brussels')
            ->url('https://spatie.be')
            ->resolvePayload();

        $this->assertPropertyEqualsInPayload('TZURL', 'https://spatie.be', $payload);
    }

    /** @test */
    public function it_can_add_timezone_entries()
    {
        $payload = Timezone::create('Europe/Brussels')
            ->entry($this->createTimezoneEntry())
            ->entry([$this->createTimezoneEntry(), $this->createTimezoneEntry()])
            ->entry(null)
            ->entry($this->createTimezoneEntry())
            ->resolvePayload();

        $this->assertCount(4, $payload->getSubComponents());
    }

    /** @test */
    public function it_can_write_out_a_timezone()
    {
        $timezone = Timezone::create('Europe/Brussels')
            ->lastModified(new DateTime('16 may 2020 12:00:00', new DateTimeZone('Europe/Brussels')))
            ->url('https://spatie.be')
            ->entry($this->createTimezoneEntry())
            ->resolvePayload();

        $output = (new ComponentBuilder($timezone))->build();

        $this->assertMatchesSnapshot($output);
    }

    private function createTimezoneEntry(): TimezoneEntry
    {
        return TimezoneEntry::create(
            TimezoneEntryType::standard(),
            new DateTime('16 may 2020 12:00:00'),
            '+00:00',
            '+02:00'
        );
    }
}
