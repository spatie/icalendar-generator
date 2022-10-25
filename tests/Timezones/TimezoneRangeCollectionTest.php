<?php

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Spatie\IcalendarGenerator\Timezones\TimezoneRangeCollection;
use Spatie\IcalendarGenerator\ValueObjects\DateTimeValue;

use function PHPUnit\Framework\assertEquals;

beforeEach(function () {
    CarbonImmutable::setTestNow('1 august 2020');
});

test('it can add dates to a range', function () {
    $d = new DateTimeImmutable('1 july 2020');
    $b = new DateTimeImmutable('13 august 2020');
    $c = new DateTimeImmutable('21 october 2020');
    $a = new DateTimeImmutable('16 may 2020');

    $ranges = TimezoneRangeCollection::create();

    $ranges->add($a);
    $ranges->add($b);
    $ranges->add($c);
    $ranges->add($d);

    assertEquals([
        'UTC' => [
            'min' => new CarbonImmutable('16 may 2020'),
            'max' => new CarbonImmutable('21 october 2020'),
        ],
    ], $ranges->get());
});

test('it can have multiple timezones', function () {
    $alternativeTimeZone = new DateTimeZone('Europe/Brussels');

    $d = new DateTimeImmutable('1 july 2020');
    $b = new DateTimeImmutable('13 august 2020');
    $c = new DateTimeImmutable('21 october 2020');
    $a = new DateTimeImmutable('16 may 2020');

    $e = new DateTimeImmutable('1 july 2020', $alternativeTimeZone);
    $f = new DateTimeImmutable('13 august 2020', $alternativeTimeZone);
    $g = new DateTimeImmutable('21 october 2020', $alternativeTimeZone);
    $h = new DateTimeImmutable('16 may 2020', $alternativeTimeZone);

    $ranges = TimezoneRangeCollection::create();

    $ranges->add($a, $b, $c, $d, $e, $f, $g, $h);

    assertEquals([
        'Europe/Brussels' => [
            'min' => new CarbonImmutable('15 may 2020 22:00:00'), // UTC transformation
            'max' => new CarbonImmutable('20 october 2020 22:00:00'), // UTC transformation
        ],
        'UTC' => [
            'min' => new CarbonImmutable('16 may 2020'),
            'max' => new CarbonImmutable('21 october 2020'),
        ],
    ], $ranges->get());
});

test('it can add different types of date times', function () {
    $d = new DateTime('1 july 2020');
    $b = new DateTimeImmutable('13 august 2020');
    $c = new CarbonImmutable('21 october 2020');
    $a = new Carbon('16 may 2020');

    $ranges = TimezoneRangeCollection::create();

    $ranges->add($a);
    $ranges->add($b);
    $ranges->add($c);
    $ranges->add($d);

    assertEquals([
        'UTC' => [
            'min' => new DateTimeImmutable('16 may 2020'),
            'max' => new DateTimeImmutable('21 october 2020'),
        ],
    ], $ranges->get());
});

test('it can add different types of entries', function () {
    $date = new DateTime('1 july 2020');
    $null = null;
    $array = [new DateTime('13 august 2020'), null, [new DateTime('16 may 2020')]];
    $otherRange = TimezoneRangeCollection::create()->add(new DateTime('21 october 2020'));
    $hasTimezones = DateTimeValue::create(new DateTime('10 september 2020'));

    $ranges = TimezoneRangeCollection::create()->add(
        $date,
        $null,
        $array,
        $otherRange,
        $hasTimezones
    );

    assertEquals([
        'UTC' => [
            'min' => new DateTimeImmutable('16 may 2020'),
            'max' => new DateTimeImmutable('21 october 2020'),
        ],
    ], $ranges->get());
});
