<?php

namespace Spatie\IcalendarGenerator\Tests\Properties;

use Spatie\IcalendarGenerator\Enums\Classification;
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
            (new TextProperty('', 'a backslash \ '))->withoutEscaping()->getValue()
        );

        $this->assertEquals(
            'a quote " ',
            (new TextProperty('', 'a quote " '))->withoutEscaping()->getValue()
        );

        $this->assertEquals(
            'a comma , ',
            (new TextProperty('', 'a comma , '))->withoutEscaping()->getValue()
        );

        $this->assertEquals(
            'a point-comma ; ',
            (new TextProperty('', 'a point-comma ; '))->withoutEscaping()->getValue()
        );

        $this->assertEquals(
            'a return \n ',
            (new TextProperty('', 'a return \n '))->withoutEscaping()->getValue()
        );
    }

    /** @test */
    public function it_can_be_created_from_an_enum()
    {
        $property = TextProperty::createFromEnum('', Classification::private());

        $this->assertEquals('PRIVATE', $property->getValue());
    }
}
