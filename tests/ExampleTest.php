<?php

namespace Spatie\Calendar\Tests;

use DateTime;
use PHPUnit\Framework\TestCase;
use Spatie\Calendar\Builder;
use Spatie\Calendar\Components\Calendar;
use Spatie\Calendar\Components\Event;

class ExampleTest extends TestCase
{
    /** @test */
    public function true_is_true()
    {
        $calendar = Calendar::create()
            ->name('PHPBenelux schedule');

        $event = Event::create()
            ->name('Hello Spatie')
            ->starts(new DateTime())
            ->ends((new DateTime())->modify('+1 day'));

        $calendar->addEvent($event);

        dump(Builder::new($calendar->getPayload())->build());
    }
}
