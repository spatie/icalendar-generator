<?php

namespace Spatie\IcalendarGenerator\Tests\Properties;

use Spatie\IcalendarGenerator\Properties\UriProperty;
use Spatie\IcalendarGenerator\Tests\TestCase;

class UriPropertyTest extends TestCase
{
    /** @test */
    public function it_accepts_an_uri()
    {
        $this->assertEquals(
            'http://this.is/a/valid/uri',
            (new UriProperty('', 'http://this.is/a/valid/uri'))->getValue()
        );

        $this->assertEquals(
            'foo://example.com:8042/over/there?name=ferret#nose',
            (new UriProperty('', 'foo://example.com:8042/over/there?name=ferret#nose'))->getValue()
        );
    }

    /** @test */
    public function it_will_return_an_empty_string_if_the_uri_is_not_valid()
    {
        $this->assertEmpty(
            (new UriProperty('', 'i-am-not-valid'))->getValue()
        );
    }
}
