<?php

namespace Spatie\IcalendarGenerator\Tests\PropertyTypes;

use Spatie\IcalendarGenerator\Tests\TestCase;
use Spatie\IcalendarGenerator\PropertyTypes\TextPropertyType;

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
}
