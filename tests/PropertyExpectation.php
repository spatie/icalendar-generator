<?php

namespace Spatie\IcalendarGenerator\Tests;

use PHPUnit\Framework\Assert;
use Spatie\IcalendarGenerator\Builders\PropertyBuilder;
use Spatie\IcalendarGenerator\Properties\Property;

class PropertyExpectation
{
    private Property $property;

    /**
     * @param \Spatie\IcalendarGenerator\Properties\Property|\Spatie\IcalendarGenerator\ComponentPayload $instance
     *
     * @return static
     */
    public static function create($instance, ?string $name = null): self
    {
        if ($instance instanceof Property) {
            return new self($instance);
        }

        return new self($instance->getProperty($name));
    }

    public function __construct(Property $property)
    {
        $this->property = $property;
    }

    public function expectInstanceOf(string $class): self
    {
        Assert::assertInstanceOf($class, $this->property);

        return $this;
    }

    public function expectName(string $name): self
    {
        Assert::assertEquals($name, $this->property->getName());

        return $this;
    }

    public function expectValue($value): self
    {
        Assert::assertEquals($value, $this->property->getOriginalValue());

        return $this;
    }

    public function expectOutput($output): self
    {
        Assert::assertEquals($output, $this->property->getValue());

        return $this;
    }

    public function expectBuilt($built): self
    {
        Assert::assertEquals($built, (new PropertyBuilder($this->property))->build()[0]);

        return $this;
    }

    public function expectParameterCount(int $count): self
    {
        Assert::assertCount(
            $count,
            $this->property->getParameters(),
            "Failed asserting that actual size ".count($this->property->getParameters())." matches expected size {$count}. Parameters: ". json_encode($this->property->getParameters())
        );

        return $this;
    }

    public function expectParameterValue(string $name, $value)
    {
        Assert::assertEquals($value, $this->property->getParameter($name)->getValue());

        return $this;
    }
}
