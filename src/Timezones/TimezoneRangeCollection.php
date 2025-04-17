<?php

namespace Spatie\IcalendarGenerator\Timezones;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

class TimezoneRangeCollection
{
    /**
     * @param array<string, array{min: DateTimeInterface, max: DateTimeInterface}> $ranges
     */
    public static function create(array $ranges = []): self
    {
        return new self($ranges);
    }

    /**
     * @param array<string, array{min: DateTimeInterface, max: DateTimeInterface}> $ranges
     */
    public function __construct(protected array $ranges = [])
    {
    }

    /**
     * @return  array<string, array{min: DateTimeInterface, max: DateTimeInterface}> $ranges
     */
    public function get(): array
    {
        return $this->ranges;
    }

    /**
     * @param array<null|TimezoneRangeCollection|DateTimeInterface|HasTimezones>|null|TimezoneRangeCollection|DateTimeInterface|HasTimezones $entries
     */
    public function add(array|null|TimezoneRangeCollection|DateTimeInterface|HasTimezones ...$entries): self
    {
        foreach ($entries as $entry) {
            if (is_array($entry)) {
                $this->addArray($entry);

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
        }

        return $this;
    }

    protected function addTimezoneRangeCollection(TimezoneRangeCollection $timezoneRangeCollection): void
    {
        foreach ($timezoneRangeCollection->get() as $timezone => $range) {
            ['min' => $minimum, 'max' => $maximum] = $range;

            $this->addEntry($timezone, $minimum);
            $this->addEntry($timezone, $maximum);
        }
    }

    protected function addDateTimeInterface(DateTimeInterface $date): void
    {
        $this->addEntry(
            $date->getTimezone()->getName(),
            $date
        );
    }

    /**
     * @param array<null|TimezoneRangeCollection|DateTimeInterface|HasTimezones> $entries
     */
    protected function addArray(array $entries): void
    {
        foreach ($entries as $entry) {
            $this->add($entry);
        }
    }

    protected function addEntry(string $timezone, DateTimeInterface $date): void
    {
        $date = DateTimeImmutable::createFromFormat(
            DATE_ATOM,
            $date->format(DATE_ATOM)
        );

        if ($date === false) {
            return;
        }

        $date = $date->setTimezone(new DateTimeZone('UTC'));

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
