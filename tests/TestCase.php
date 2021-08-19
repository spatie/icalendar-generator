<?php

namespace Spatie\IcalendarGenerator\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Spatie\IcalendarGenerator\Builders\PropertyBuilder;
use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Properties\Property;
use Spatie\Snapshots\MatchesSnapshots;

abstract class TestCase extends BaseTestCase
{
    use MatchesSnapshots;
}
