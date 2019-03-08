<?php

namespace Spatie\Calendar\Tests;

use DateTime;
use PHPUnit\Framework\TestCase;
use Spatie\Calendar\Components\Calendar;
use Spatie\Calendar\Components\Event;

class ExampleTest extends TestCase
{
    /** @test */
    public function true_is_true()
    {
        $calendar = Calendar::name('PHPBenelux schedule')
            ->event(function (Event $event) {
                return $event->name('Hello Spatie')
                    ->starts(new DateTime())
                    ->ends((new DateTime())->modify('+1 day'));
            })
            ->toString();
    }
}
