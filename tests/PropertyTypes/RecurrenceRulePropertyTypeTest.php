<?php

namespace Spatie\IcalendarGenerator\Tests\PropertyTypes;

use Spatie\IcalendarGenerator\Enums\RecurrenceFrequency;
use Spatie\IcalendarGenerator\PropertyTypes\RecurrenceRulePropertyType;
use Spatie\IcalendarGenerator\Tests\TestCase;
use Spatie\IcalendarGenerator\ValueObjects\RecurrenceRule;

class RecurrenceRulePropertyTypeTest extends TestCase
{
    /** @test */
    public function it_can_create_a_recurrence_rule_property_type()
    {
        $recurrenceRule = RecurrenceRule::frequency(RecurrenceFrequency::daily());

        $propertyType = RecurrenceRulePropertyType::create([
            'RRULE'
        ], $recurrenceRule);

        $this->assertEquals(['RRULE'], $propertyType->getNames());
        $this->assertEquals('RRULE:FREQ=DAILY', $propertyType->getValue());
        $this->assertEquals($recurrenceRule, $propertyType->getOriginalValue());
    }
}
