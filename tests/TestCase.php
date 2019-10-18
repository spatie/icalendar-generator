<?php

namespace Spatie\IcalendarGenerator\Tests;

use Spatie\IcalendarGenerator\ComponentPayload;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Spatie\IcalendarGenerator\PropertyTypes\PropertyType;

abstract class TestCase extends BaseTestCase
{
    protected function assertInArray($needle, $haystack): void
    {
        $this->assertContains($needle, $haystack, '', false, false);
    }

    protected function assertPropertyExistInPayload(string $name, ComponentPayload $componentPayload): void
    {
        $this->assertNotNull($componentPayload->getProperty($name));
    }

    protected function assertPropertyEqualsInPayload(string $name, $value, ComponentPayload $componentPayload): void
    {
        $this->assertEquals($value, $componentPayload->getProperty($name)->getOriginalValue());
    }

    protected function assertParameterEqualsInProperty(string $name, $value, PropertyType $propertyType): void
    {
        $this->assertEquals($value, $propertyType->getParameter($name)->getValue());
    }
}
