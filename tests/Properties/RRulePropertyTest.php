<?php

namespace Spatie\IcalendarGenerator\Tests\Properties;

use Spatie\IcalendarGenerator\Enums\RecurrenceFrequency;
use Spatie\IcalendarGenerator\Properties\RRuleProperty;
use Spatie\IcalendarGenerator\Tests\TestCase;
use Spatie\IcalendarGenerator\ValueObjects\RRule;

class RRulePropertyTest extends TestCase
{
    /** @test */
    public function it_can_create_a_recurrence_rule_property_type()
    {
        $recurrenceRule = RRule::frequency(RecurrenceFrequency::daily());

        $propertyType = RRuleProperty::create('RRULE', $recurrenceRule);

        $this->assertEquals('RRULE', $propertyType->getName());
        $this->assertEquals('FREQ=DAILY', $propertyType->getValue());
        $this->assertEquals($recurrenceRule, $propertyType->getOriginalValue());
    }
}
