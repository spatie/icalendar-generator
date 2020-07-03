<?php

namespace Spatie\IcalendarGenerator\Tests\Properties;

use DateTime;
use DateTimeZone;
use Spatie\IcalendarGenerator\Properties\DateTimeProperty;
use Spatie\IcalendarGenerator\Tests\TestCase;

class DateTimePropertyTest extends TestCase
{
    protected DateTime $date;

    protected function setUp() : void
    {
        parent::setUp();

        $this->date = (new DateTime('16 may 2019 12:10:15', new DateTimeZone('Europe/Brussels')));
    }

    /** @test */
    public function it_will_format_the_date_correctly()
    {
        $property = new DateTimeProperty('STARTS', $this->date);

        $this->assertEquals('20190516', $property->getValue());
    }

    /** @test */
    public function it_will_format_the_date_and_time_correctly_with_timezone()
    {
        $property = new DateTimeProperty('STARTS', $this->date, true);

        $this->assertEquals('20190516T121015', $property->getValue());
        $this->assertCount(1, $property->getParameters());
        $this->assertParameterEqualsInProperty('TZID', 'Europe/Brussels', $property);
    }

    /** @test */
    public function it_will_format_the_date_and_time_correctly_with_a_conversion_to_utc_timezone()
    {
        $this->date->setTimezone(new DateTimeZone('UTC'));

        $property = new DateTimeProperty('STARTS', $this->date, true);

        $this->assertEquals('20190516T101015', $property->getValue());
        $this->assertCount(1, $property->getParameters());
        $this->assertParameterEqualsInProperty('TZID', 'UTC', $property);
    }

    /** @test */
    public function it_will_format_the_date_and_time_without_timezone()
    {
        $property = new DateTimeProperty('STARTS', $this->date, true, true);

        $this->assertEquals('20190516T121015', $property->getValue());
    }
}
