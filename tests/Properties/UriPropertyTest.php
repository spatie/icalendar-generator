<?php

namespace Spatie\IcalendarGenerator\Tests\Properties;

use Spatie\IcalendarGenerator\Properties\UriProperty;
use Spatie\IcalendarGenerator\Tests\PropertyExpectation;
use Spatie\IcalendarGenerator\Tests\TestCase;

class UriPropertyTest extends TestCase
{
    /** @test */
    public function it_accepts_an_uri()
    {
        PropertyExpectation::create(new UriProperty('', 'http://this.is/a/valid/uri'))
            ->expectValue('http://this.is/a/valid/uri');

        PropertyExpectation::create(new UriProperty('', 'foo://example.com:8042/over/there?name=ferret#nose'))
            ->expectValue('foo://example.com:8042/over/there?name=ferret#nose');
    }

    /** @test */
    public function it_will_return_an_empty_string_if_the_uri_is_not_valid()
    {
        PropertyExpectation::create(new UriProperty('', 'i-am-not-valid'))->expectOutput('');
    }
}
