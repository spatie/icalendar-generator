<?php

namespace Spatie\IcalendarGenerator\Tests\Properties;

use DateTime;
use DateTimeZone;
use Spatie\IcalendarGenerator\Properties\DateTimeProperty;
use Spatie\IcalendarGenerator\Tests\PropertyExpectation;
use Spatie\IcalendarGenerator\Tests\TestCase;
use Spatie\IcalendarGenerator\ValueObjects\DateTimeValue;

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
        $property = DateTimeProperty::fromDateTime('STARTS', $this->date);

        PropertyExpectation::create($property)->expectOutput('20190516');
    }

    /** @test */
    public function it_will_format_the_date_and_time_correctly_with_timezone()
    {
        $property = DateTimeProperty::fromDateTime('STARTS', $this->date, true);

        PropertyExpectation::create($property)
            ->expectOutput('20190516T121015')
            ->expectParameterCount(1)
            ->expectParameterValue('TZID', 'Europe/Brussels');
    }

    /** @test */
    public function it_will_use_a_specific_utc_format()
    {
        $this->date->setTimezone(new DateTimeZone('UTC'));

        $property = DateTimeProperty::fromDateTime('STARTS', $this->date, true);

        PropertyExpectation::create($property)
            ->expectOutput('20190516T101015Z')
            ->expectParameterCount(0);
    }

    /** @test */
    public function it_will_not_use_a_specific_utc_format_when_time_is_not_given()
    {
        $this->date->setTimezone(new DateTimeZone('UTC'));

        $property = DateTimeProperty::fromDateTime('STARTS', $this->date);

        PropertyExpectation::create($property)
            ->expectOutput('20190516')
            ->expectParameterCount(1)
            ->expectParameterValue('VALUE', 'DATE');
    }

    /** @test */
    public function it_will_use_a_non_utc_timezone_format_when_time_is_not_given()
    {
        $property = DateTimeProperty::fromDateTime('STARTS', $this->date, false);

        PropertyExpectation::create($property)
            ->expectOutput('20190516')
            ->expectParameterCount(2)
            ->expectParameterValue('TZID', 'Europe/Brussels')
            ->expectParameterValue('VALUE', 'DATE');
    }

    /** @test */
    public function it_will_format_the_date_and_time_correctly_with_a_conversion_to_another_timezone()
    {
        $this->date->setTimezone(new DateTimeZone('Europe/Brussels'));

        $property = DateTimeProperty::fromDateTime('STARTS', $this->date, true);

        PropertyExpectation::create($property)
            ->expectOutput('20190516T121015')
            ->expectParameterCount(1)
            ->expectParameterValue('TZID', 'Europe/Brussels');
    }

    /** @test */
    public function it_will_format_the_date_and_time_without_timezone()
    {
        $property = DateTimeProperty::fromDateTime('STARTS', $this->date, true, true);

        PropertyExpectation::create($property)->expectOutput('20190516T121015');
    }

    /** @test */
    public function it_can_be_created_from_a_date_time_value()
    {
        $property = DateTimeProperty::create(
            'STARTS',
            DateTimeValue::create($this->date)
        );

        PropertyExpectation::create($property)->expectOutput('20190516T121015');
    }
}
