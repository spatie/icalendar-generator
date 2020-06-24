<?php

namespace Spatie\IcalendarGenerator\Tests\Properties;

use Spatie\IcalendarGenerator\Enums\RecurrenceFrequency;
use Spatie\IcalendarGenerator\Properties\RecurrenceRuleProperty;
use Spatie\IcalendarGenerator\Tests\TestCase;
use Spatie\IcalendarGenerator\ValueObjects\RecurrenceRule;

class RecurrenceRulePropertyTest extends TestCase
{
    /** @test */
    public function it_can_create_a_recurrence_rule_property_type()
    {
        $recurrenceRule = RecurrenceRule::frequency(RecurrenceFrequency::daily());

        $propertyType = RecurrenceRuleProperty::create('RRULE', $recurrenceRule);

        $this->assertEquals('RRULE', $propertyType->getName());
        $this->assertEquals('RRULE:FREQ=DAILY', $propertyType->getValue());
        $this->assertEquals($recurrenceRule, $propertyType->getOriginalValue());
    }
}
