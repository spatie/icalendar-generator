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

        $this->date = new DateTime('16 may 2019 12:10:15');
    }

    /** @test */
    public function it_will_format_the_date_correctly()
    {
        $property = new DateTimeProperty('STARTS', $this->date);

        $this->assertEquals('20190516', $property->getValue());
    }

    /** @test */
    public function it_will_format_the_date_and_time_correctly()
    {
        $property = new DateTimeProperty('STARTS', $this->date, true);

        $this->assertEquals('20190516T121015', $property->getValue());
    }

    /** @test */
    public function it_will_format_the_date_and_time_and_timezone_correctly()
    {
        $this->date->setTimezone(new DateTimeZone('Europe/Brussels'));

        $property = new DateTimeProperty('STARTS', $this->date, true, true);

        $this->assertEquals('20190516T141015', $property->getValue());
        $this->assertEquals(1, count($property->getParameters()));
    }
}
