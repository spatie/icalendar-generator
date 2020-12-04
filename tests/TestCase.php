<?php

namespace Spatie\IcalendarGenerator\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Spatie\IcalendarGenerator\Builders\PropertyBuilder;
use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Properties\Property;
use Spatie\Snapshots\MatchesSnapshots;

abstract class TestCase extends BaseTestCase
{
    use MatchesSnapshots;

    protected function assertPropertyExistInPayload(string $name, ComponentPayload $componentPayload): void
    {
        $this->assertNotNull($componentPayload->getProperty($name));
    }

    protected function assertPropertyNotInPayload(string $name, ComponentPayload $componentPayload): void
    {
        $this->assertObjectNotHasAttribute($name, $componentPayload);
    }

    protected function assertPropertyEqualsInPayload(string $name, $value, ComponentPayload $componentPayload): void
    {
        $this->assertEquals($value, $componentPayload->getProperty($name)->getOriginalValue());
    }

    protected function assertParameterEqualsInProperty(string $name, $value, Property $propertyType): void
    {
        $this->assertEquals($value, $propertyType->getParameter($name)->getValue());
    }

    protected function assertParameterCountInProperty(int $count, Property $propertyType): void
    {
        $this->assertCount($count, $propertyType->getParameters());
    }

    protected function assertBuildPropertyEqualsInPayload(string $name, string $value, ComponentPayload $componentPayload)
    {
        $buildValue = (new PropertyBuilder($componentPayload->getProperty($name)))->build()[0];

        $this->assertEquals($value, $buildValue);
    }
}
