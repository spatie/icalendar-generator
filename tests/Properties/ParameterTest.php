<?php

namespace Spatie\IcalendarGenerator\Tests\Properties;

use DateTime;
use Spatie\IcalendarGenerator\Enums\EventStatus;
use Spatie\IcalendarGenerator\Properties\Parameter;
use Spatie\IcalendarGenerator\Tests\TestCase;
use Spatie\IcalendarGenerator\ValueObjects\DateTimeValue;

class ParameterTest extends TestCase
{
    /** @test */
    public function it_replaces_all_illegal_characters()
    {
        $this->assertEquals(
            'a backslash \\\\ ',
            (new Parameter('', 'a backslash \ '))->getValue()
        );

        $this->assertEquals(
            'a quote \\" ',
            (new Parameter('', 'a quote " '))->getValue()
        );

        $this->assertEquals(
            'a comma \\, ',
            (new Parameter('', 'a comma , '))->getValue()
        );

        $this->assertEquals(
            'a point-comma \\; ',
            (new Parameter('', 'a point-comma ; '))->getValue()
        );

        $this->assertEquals(
            'a return \\\n ',
            (new Parameter('', 'a return \n '))->getValue()
        );
    }

    /** @test */
    public function it_can_disable_escaping()
    {
        $this->assertEquals(
            'a backslash \ ',
            (new Parameter('', 'a backslash \ ', true))->getValue()
        );

        $this->assertEquals(
            'a quote " ',
            (new Parameter('', 'a quote " ', true))->getValue()
        );

        $this->assertEquals(
            'a comma , ',
            (new Parameter('', 'a comma , ', true))->getValue()
        );

        $this->assertEquals(
            'a point-comma ; ',
            (new Parameter('', 'a point-comma ; ', true))->getValue()
        );

        $this->assertEquals(
            'a return \n ',
            (new Parameter('', 'a return \n ', true))->getValue()
        );
    }

    /** @test */
    public function it_can_format_a_boolean()
    {
        $this->assertEquals(
            'BOOLEAN:TRUE',
            (new Parameter('', true))->getValue()
        );

        $this->assertEquals(
            'BOOLEAN:FALSE',
            (new Parameter('', false))->getValue()
        );
    }

    /** @test */
    public function it_can_format_an_enum()
    {
        $this->assertEquals(
            'CANCELLED',
            (new Parameter('', EventStatus::cancelled()))->getValue()
        );
    }

    /** @test */
    public function it_can_format_a_date_time_value()
    {
        $dateTime = new DateTime('16 may 1994');

        $this->assertEquals(
            'DATE-TIME:19940516T000000',
            (new Parameter('', DateTimeValue::create($dateTime)))->getValue()
        );

        $this->assertEquals(
            'DATE:19940516',
            (new Parameter('', DateTimeValue::create($dateTime, false)))->getValue()
        );
    }
}
