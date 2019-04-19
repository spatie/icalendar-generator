<?php

namespace Spatie\Calendar\Tests;

use Spatie\Calendar\ComponentPayload;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Spatie\Calendar\PropertyTypes\PropertyType;

abstract class TestCase extends BaseTestCase
{
    public function assertInArray($needle, $haystack): void
    {
        $this->assertContains($needle, $haystack, '', false, false);
    }

    public function assertPropertyExistInPayload(string $name, ComponentPayload $componentPayload): void
    {
        $this->assertNotNull($componentPayload->getProperty($name));
    }

    public function assertPropertyEqualsInPayload(string $name, $value, ComponentPayload $componentPayload): void
    {
        $this->assertEquals($value, $componentPayload->getProperty($name)->getOriginalValue());
    }

    public function assertAliasEqualsForProperty(string $propertyName, array $aliases, ComponentPayload $componentPayload): void
    {
        $this->assertEquals($aliases, $componentPayload->getAliasesForProperty($propertyName));
    }

    public function assertParameterEqualsInProperty(string $name, $value, PropertyType $propertyType): void
    {
        $this->assertEquals($value, $propertyType->getParameter($name)->getValue());
    }
}
