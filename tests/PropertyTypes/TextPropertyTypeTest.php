<?php

namespace Spatie\IcalendarGenerator\Tests\PropertyTypes;

use Spatie\IcalendarGenerator\PropertyTypes\TextPropertyType;
use Spatie\IcalendarGenerator\Tests\TestCase;

class TextPropertyTypeTest extends TestCase
{
    /** @test */
    public function it_replaces_all_illegal_characters()
    {
        $this->assertEquals(
            'a backslash \\\\ ',
            (new TextPropertyType('', 'a backslash \ '))->getValue()
        );

        $this->assertEquals(
            'a quote \\" ',
            (new TextPropertyType('', 'a quote " '))->getValue()
        );

        $this->assertEquals(
            'a comma \\, ',
            (new TextPropertyType('', 'a comma , '))->getValue()
        );

        $this->assertEquals(
            'a point-comma \\; ',
            (new TextPropertyType('', 'a point-comma ; '))->getValue()
        );

        $this->assertEquals(
            'a return \\\n ',
            (new TextPropertyType('', 'a return \n '))->getValue()
        );
    }

    /** @test */
    public function it_can_disable_escaping()
    {
        $this->assertEquals(
            'a backslash \ ',
            (new TextPropertyType('', 'a backslash \ ', true))->getValue()
        );

        $this->assertEquals(
            'a quote " ',
            (new TextPropertyType('', 'a quote " ', true))->getValue()
        );

        $this->assertEquals(
            'a comma , ',
            (new TextPropertyType('', 'a comma , ', true))->getValue()
        );

        $this->assertEquals(
            'a point-comma ; ',
            (new TextPropertyType('', 'a point-comma ; ', true))->getValue()
        );

        $this->assertEquals(
            'a return \n ',
            (new TextPropertyType('', 'a return \n ', true))->getValue()
        );
    }
}
