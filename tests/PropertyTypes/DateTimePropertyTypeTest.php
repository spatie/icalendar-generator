<?php

namespace Spatie\IcalendarGenerator\Tests\PropertyTypes;

use DateTime;
use DateTimeZone;
use Spatie\IcalendarGenerator\PropertyTypes\DateTimePropertyType;
use Spatie\IcalendarGenerator\Tests\TestCase;

class DateTimePropertyTypeTest extends TestCase
{
    /** @var \DateTime */
    protected $date;

    protected function setUp() : void
    {
        parent::setUp();

        $this->date = new DateTime('16 may 2019 12:10:15');
    }

    /** @test */
    public function it_will_format_the_date_correctly()
    {
        $property = new DateTimePropertyType('STARTS', $this->date);

        $this->assertEquals('20190516', $property->getValue());
    }

    /** @test */
    public function it_will_format_the_date_and_time_correctly()
    {
        $property = new DateTimePropertyType('STARTS', $this->date, true);

        $this->assertEquals('20190516T121015', $property->getValue());
    }

    /** @test */
    public function it_will_format_the_date_and_time_and_timezone_correctly()
    {
        $this->date->setTimezone(new DateTimeZone('Europe/Brussels'));

        $property = new DateTimePropertyType('STARTS', $this->date, true, true);

        $this->assertEquals('20190516T141015', $property->getValue());
        $this->assertEquals(1, count($property->getParameters()));
    }
}
