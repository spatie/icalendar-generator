<?php

namespace Spatie\Calendar\Tests;

use \PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function assertInArray($needle, $haystack) : void
    {
        $this->assertContains($needle, $haystack, '', false, false);
    }
}
