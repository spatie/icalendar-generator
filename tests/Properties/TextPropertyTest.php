<?php

namespace Spatie\IcalendarGenerator\Tests\Properties;

use Spatie\IcalendarGenerator\Properties\TextProperty;
use Spatie\IcalendarGenerator\Tests\TestCase;

class TextPropertyTest extends TestCase
{
    /** @test */
    public function it_replaces_all_illegal_characters()
    {
        $this->assertEquals(
            'a backslash \\\\ ',
            (new TextProperty('', 'a backslash \ '))->getValue()
        );

        $this->assertEquals(
            'a quote \\" ',
            (new TextProperty('', 'a quote " '))->getValue()
        );

        $this->assertEquals(
            'a comma \\, ',
            (new TextProperty('', 'a comma , '))->getValue()
        );

        $this->assertEquals(
            'a point-comma \\; ',
            (new TextProperty('', 'a point-comma ; '))->getValue()
        );

        $this->assertEquals(
            'a return \\\n ',
            (new TextProperty('', 'a return \n '))->getValue()
        );
    }

    /** @test */
    public function it_can_disable_escaping()
    {
        $this->assertEquals(
            'a backslash \ ',
            (new TextProperty('', 'a backslash \ ', true))->getValue()
        );

        $this->assertEquals(
            'a quote " ',
            (new TextProperty('', 'a quote " ', true))->getValue()
        );

        $this->assertEquals(
            'a comma , ',
            (new TextProperty('', 'a comma , ', true))->getValue()
        );

        $this->assertEquals(
            'a point-comma ; ',
            (new TextProperty('', 'a point-comma ; ', true))->getValue()
        );

        $this->assertEquals(
            'a return \n ',
            (new TextProperty('', 'a return \n ', true))->getValue()
        );
    }
}
