<?php

namespace Spatie\Calendar\Tests;

use Exception;
use \PHPUnit\Framework\TestCase as BaseTestCase;
use Spatie\Calendar\ComponentPayload;

abstract class TestCase extends BaseTestCase
{
    public function assertInArray($needle, $haystack): void
    {
        $this->assertContains($needle, $haystack, '', false, false);
    }

    public function assertPropertyExistInPayload(string $name, ComponentPayload $componentPayload): void
    {
        foreach ($componentPayload->getProperties() as $property) {
            if ($property->getName() === $name) {
                $this->assertTrue(true);

                return;
            }
        }

        $this->assertTrue(false);
    }

    public function assertPropertyEqualsInPayload(string $name, $value, ComponentPayload $componentPayload): void
    {
        foreach ($componentPayload->getProperties() as $property) {
            if ($property->getName() === $name) {
                $this->assertEquals($property->getOriginalValue(), $value);

                return;
            }
        }

        throw new Exception("Property {$name} not found!");
    }
}
