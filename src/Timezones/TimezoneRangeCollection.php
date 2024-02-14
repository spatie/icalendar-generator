<?php

namespace Spatie\IcalendarGenerator\Timezones;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
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
        $date = DateTimeImmutable::createFromFormat(
            DATE_ATOM,
            $date->format(DATE_ATOM)
        )->setTimezone(new DateTimeZone('UTC'));

        if (! array_key_exists($timezone, $this->ranges)) {
            $this->ranges[$timezone] = [
                'min' => $this->getMaximumDateTimeImmutable(),
                'max' => $this->getMinimumDateTimeImmutable(),
            ];
        }

        /** @var DateTimeInterface $minimum */
        $minimum = $this->ranges[$timezone]['min'];

        if ($date < $minimum) {
            $this->ranges[$timezone]['min'] = $date;
        }

        /** @var DateTimeInterface $maximum */
        $maximum = $this->ranges[$timezone]['max'];

        if ($date > $maximum) {
            $this->ranges[$timezone]['max'] = $date;
        }
    }

    protected function getMinimumDateTimeImmutable(): DateTimeImmutable
    {
        return PHP_INT_SIZE === 4 ?
            (new DateTimeImmutable())->setTimestamp(~PHP_INT_MAX) :
            (new DateTimeImmutable('0001-01-01 0:0:0'));
    }

    protected function getMaximumDateTimeImmutable(): DateTimeImmutable
    {
        return PHP_INT_SIZE === 4 ?
            (new DateTimeImmutable())->setTimestamp(PHP_INT_MAX) :
            (new DateTimeImmutable('9999-12-31 23:59:59'));
    }
}
