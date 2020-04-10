<?php

namespace Spatie\IcalendarGenerator\Tests\PropertyTypes;

use Spatie\IcalendarGenerator\PropertyTypes\Parameter;
use Spatie\IcalendarGenerator\Tests\TestCase;

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
}
