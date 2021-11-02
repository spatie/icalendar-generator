<?php

namespace Spatie\IcalendarGenerator\Timezones;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use Exception;

class TimezoneRangeCollection
{
    private array $ranges;

    public static function create(array $ranges = []): self
    {
        return new self($ranges);
    }

    public function __construct(array $ranges = [])
    {
        $this->ranges = $ranges;
    }

    public function get(): array
    {
        return $this->ranges;
    }

    /**
     * @param array|null|\Spatie\IcalendarGenerator\Timezones\TimezoneRangeCollection|DateTimeInterface|\Spatie\IcalendarGenerator\Timezones\HasTimezones $entries
     *
     * @return \Spatie\IcalendarGenerator\Timezones\TimezoneRangeCollection
     * @throws \Exception
     */
    public function add(...$entries): self
    {
        foreach ($entries as $entry) {
            if (is_array($entry)) {
                $this->addArray($entry);

                continue;
            }

            if ($entry === null) {
                continue;
            }

            if ($entry instanceof DateTimeInterface) {
                $this->addDateTimeInterface($entry);

                continue;
            }

            if ($entry instanceof HasTimezones) {
                $this->addTimezoneRangeCollection(
                    $entry->getTimezoneRangeCollection()
                );

                continue;
            }

            if ($entry instanceof TimezoneRangeCollection) {
                $this->addTimezoneRangeCollection($entry);

                continue;
            }

            throw new Exception('Could not add entry to TimeZoneRangeCollection');
        }

        return $this;
    }

    private function addTimezoneRangeCollection(TimezoneRangeCollection $timezoneRangeCollection)
    {
        foreach ($timezoneRangeCollection->get() as $timezone => $range) {
            ['min' => $minimum, 'max' => $maximum] = $range;

            $this->addEntry($timezone, $minimum);
            $this->addEntry($timezone, $maximum);
        }
    }

    private function addDateTimeInterface(DateTimeInterface $date): void
    {
        $this->addEntry(
            $date->getTimezone()->getName(),
            $date
        );
    }

    private function addArray(array $entries)
    {
        foreach ($entries as $entry) {
            $this->add($entry);
        }
    }

    private function addEntry(string $timezone, DateTimeInterface $date)
    {
        $date = CarbonImmutable::createFromFormat(
            DATE_ATOM,
            $date->format(DATE_ATOM)
        )->setTimezone('UTC');

        if (! array_key_exists($timezone, $this->ranges)) {
            $this->ranges[$timezone] = [
                'min' => CarbonImmutable::maxValue(),
                'max' => CarbonImmutable::minValue(),
            ];
        }

        /** @var \Carbon\CarbonImmutable $minimum */
        $minimum = $this->ranges[$timezone]['min'];

        if ($date->lt($minimum)) {
            $this->ranges[$timezone]['min'] = $date;
        }

        /** @var \Carbon\CarbonImmutable $maximum */
        $maximum = $this->ranges[$timezone]['max'];

        if ($date->gt($maximum)) {
            $this->ranges[$timezone]['max'] = $date;
        }
    }
}
