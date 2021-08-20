<?php

namespace Spatie\IcalendarGenerator\Tests;

use Closure;
use PHPUnit\Framework\Assert;
use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Components\Component;
use Spatie\IcalendarGenerator\Properties\Property;

class PayloadExpectation
{
    private ComponentPayload $payload;

    public static function create(ComponentPayload $payload): self
    {
        return new self($payload);
    }

    public function __construct(ComponentPayload $payload)
    {
        $this->payload = $payload;
    }

    public function expectType(string $type): self
    {
        Assert::assertEquals($type, $this->payload->getType());

        return $this;
    }

    public function expectPropertyCount(int $count): self
    {
        Assert::assertCount($count, $this->payload->getProperties());

        return $this;
    }

    public function expectProperty(string $name, Closure  ...$closures): self
    {
        $properties = $this->getProperties($name);

        foreach ($closures as $index => $closure) {
            ($closure)(PropertyExpectation::create($properties[$index]));
        }

        return $this;
    }

    public function expectPropertyValue(string $name, ...$values): self
    {
        $propertyValues = array_map(function (Property $property) {
            return $property->getOriginalValue();
        }, $this->getProperties($name));

        foreach ($values as $value) {
            if (in_array($value, $propertyValues)) {
                Assert::assertTrue(true);
            } else {
                Assert::assertTrue(false, "Could not find property with name: {$name} and value: {$value}");
            }
        }

        return $this;
    }

    public function expectPropertyExists(string $name): self
    {
        Assert::assertNotEmpty($this->getProperties($name));

        return $this;
    }

    public function expectPropertyMissing(string $name): self
    {
        Assert::assertObjectNotHasAttribute($name, $this->payload);

        return $this;
    }

    public function expectSubComponentCount(int $count): self
    {
        Assert::assertCount($count, $this->payload->getSubComponents());

        return $this;
    }

    public function expectSubComponents(Component ...$component): self
    {
        Assert::assertEqualsCanonicalizing($component, $this->payload->getSubComponents());

        return $this;
    }

    public function expectSubComponent(int $index, Closure $closure): self
    {
        /** @var \Spatie\IcalendarGenerator\Components\Component $subcomponent */
        $subcomponent = $this->payload->getSubComponents()[$index];

        $closure(PayloadExpectation::create($subcomponent->resolvePayload()));

        return $this;
    }

    public function expectSubComponentInstanceOf(int $index, string $class): self
    {
        /** @var \Spatie\IcalendarGenerator\Components\Component $subcomponent */
        $subcomponent = $this->payload->getSubComponents()[$index];

        Assert::assertInstanceOf($class, $subcomponent);

        return $this;
    }

    public function expectSubComponentNotInstanceOf(int $index, string $class): self
    {
        /** @var \Spatie\IcalendarGenerator\Components\Component $subcomponent */
        $subcomponent = $this->payload->getSubComponents()[$index];

        Assert::assertNotInstanceOf($class, $subcomponent);

        return $this;
    }

    /**
     * @param string $name
     *
     * @return \Spatie\IcalendarGenerator\Properties\Property[]
     * @throws \Exception
     */
    private function getProperties(string $name): array
    {
        $filteredProperties = array_filter(
            $this->payload->getProperties(),
            function (Property $property) use ($name) {
                return in_array($name, $property->getNameAndAliases());
            }
        );

        return array_values($filteredProperties);
    }
}
