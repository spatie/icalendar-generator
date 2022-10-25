<?php

use Spatie\IcalendarGenerator\Enums\RecurrenceDay;
use Spatie\IcalendarGenerator\Enums\RecurrenceMonth;

dataset('week-days', [
    [
        'days' => [
            ['day' => RecurrenceDay::monday(), 'index' => null],
        ],
        'expected' => 'MO',
    ],
    [
        'days' => [
            ['day' => RecurrenceDay::monday(), 'index' => 2],
        ],
        'expected' => '2MO',
    ],
    [
        'days' => [
            ['day' => RecurrenceDay::monday(), 'index' => -1],
        ],
        'expected' => '-1MO',
    ],
    [
        'days' => [
            ['day' => RecurrenceDay::monday(), 'index' => null],
            ['day' => RecurrenceDay::monday(), 'index' => 2],
            ['day' => RecurrenceDay::monday(), 'index' => -1],
        ],
        'expected' => 'MO,2MO,-1MO',
    ],
    [
        'days' => [
            ['day' => RecurrenceDay::monday(), 'index' => null],
            ['day' => RecurrenceDay::monday(), 'index' => null],
            ['day' => RecurrenceDay::monday(), 'index' => -1],
            ['day' => RecurrenceDay::monday(), 'index' => -1],
        ],
        'expected' => 'MO,-1MO',
    ],
]);

dataset('months', [
    [
        'months' => RecurrenceMonth::may(),
        'expected' => '5',
    ],
    [
        'months' => 5,
        'expected' => '5',
    ],
    [
        'months' => [RecurrenceMonth::april(), RecurrenceMonth::may()],
        'expected' => '4,5',
    ],
    [
        'months' => [4, 5],
        'expected' => '4,5',
    ],
    [
        'months' => [RecurrenceMonth::may(), 5],
        'expected' => '5',
    ],
]);

dataset('month-days', [
    [
        'monthsDays' => 1,
        'expected' => '1',
    ],
    [
        'monthsDays' => [1, 2, 3],
        'expected' => '1,2,3',
    ],
    [
        'monthsDays' => [1, 2, 1],
        'expected' => '1,2',
    ],
]);
