<?php

use Spatie\IcalendarGenerator\Enums\RecurrenceFrequency;
use Spatie\IcalendarGenerator\Properties\RRuleProperty;
use Spatie\IcalendarGenerator\Tests\PropertyExpectation;
use Spatie\IcalendarGenerator\ValueObjects\RRule;

test('it can create a recurrence rule property type', function () {
    $recurrenceRule = RRule::frequency(RecurrenceFrequency::Daily);

    $propertyType = RRuleProperty::create('RRULE', $recurrenceRule);

    PropertyExpectation::create($propertyType)
        ->expectName('RRULE')
        ->expectOutput('FREQ=DAILY')
        ->expectValue($recurrenceRule);
});
