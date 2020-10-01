<?php

namespace Spatie\IcalendarGenerator\Tests\PropertyTypes;

use Spatie\IcalendarGenerator\PropertyTypes\UriPropertyType;
use Spatie\IcalendarGenerator\Tests\TestCase;

class UriPropertyTypeTest extends TestCase
{
    /** @test */
    public function it_accepts_only_valid_uri()
    {
        $this->assertEquals(
            'http://this.is/a/valid/uri',
            (new UriPropertyType('', 'http://this.is/a/valid/uri'))->getValue()
        );

        $this->assertEquals(
            'foo://example.com:8042/over/there?name=ferret#nose',
            (new UriPropertyType('', 'foo://example.com:8042/over/there?name=ferret#nose'))->getValue()
        );

        $this->assertEquals(
            null,
            (new UriPropertyType('', 'wrong.uri/is?set=true'))->getValue()
        );
    }
}
