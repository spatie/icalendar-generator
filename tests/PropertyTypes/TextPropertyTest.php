<?php

namespace Spatie\Calendar\Tests\PropertyTypes;

use Spatie\Calendar\Tests\TestCase;
use Spatie\Calendar\PropertyTypes\TextProperty;

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
}
