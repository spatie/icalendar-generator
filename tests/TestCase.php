<?php

namespace Spatie\IcalendarGenerator\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

abstract class TestCase extends BaseTestCase
{
    use MatchesSnapshots;
}
