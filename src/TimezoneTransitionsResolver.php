<?php

namespace Spatie\IcalendarGenerator;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Spatie\IcalendarGenerator\Enums\TimezoneEntryType;
use Spatie\IcalendarGenerator\ValueObjects\TimezoneTransition;

class TimezoneTransitionsResolver
{
    private DateTimeZone $timeZone;

    private DateTimeImmutable $start;

    private DateTimeImmutable $end;

    public function __construct(
        DateTimeZone $timeZone,
        DateTimeInterface $start,
        DateTimeInterface $end
    ) {
        $this->timeZone = $timeZone;
        $this->start = (new DateTimeImmutable($start->format(DATE_ATOM)))->sub(
            new DateInterval('P270D'),
        );
        $this->end = (new DateTimeImmutable($end->format(DATE_ATOM)))->add(
            new DateInterval('P185D')
        );
    }

    public function getTransitions(): array
    {
        $transitions = $this->timeZone->getTransitions(
            $this->start->getTimestamp(),
            $this->end->getTimestamp(),
        );

        if (count($transitions) === 1) {
            // Add a fake transition for UTC for example
            $transitions[] = [
                'isdst' => $transitions[0]["isdst"],
                'offset' => $transitions[0]["offset"],
                'ts' => $this->start->getTimestamp(),
                'abbr' => $transitions[0]["abbr"],
            ];
        }

        $found = [];
        $lastTransition = $transitions[0];

        // Skip the first to determine the offset
        for ($i = 1; $i < count($transitions); $i++) {
            $transition = $transitions[$i];

            $type = $transition['isdst']
                ? TimezoneEntryType::daylight()
                : TimezoneEntryType::standard();

            $offsetFrom = $this->resolveOffset((int) $lastTransition['offset']);
            $offsetTo = $this->resolveOffset((int) $transition['offset']);

            $offsetDiff = $this->resolveOffsetDiff($offsetFrom, $offsetTo);

            $found[] = new TimezoneTransition(
                $this->resolveStartDate($transition['ts'], $offsetDiff),
                $offsetFrom,
                $offsetTo,
                $type
            );

            $lastTransition = $transition;
        }

        return $found;
    }

    private function resolveOffset(int $offset): DateInterval
    {
        $hours = floor($offset / 3600);
        $minutes = abs(($offset / 60) % 60);

        $interval = new DateInterval(
            'PT' . abs($hours) . 'H' . abs($minutes) . 'M'
        );

        $interval->invert = $hours < 0 || $minutes < 0;

        return $interval;
    }

    private function resolveOffsetDiff(DateInterval $from, DateInterval $to): DateInterval
    {
        $hours = (int) $from->format('%r%h') - (int) $to->format('%r%h');
        $minutes = (int) $from->format('%r%m') - (int) $to->format('%r%m');

        $interval = new DateInterval(
            'PT' . abs($hours) . 'H' . abs($minutes) . 'M'
        );

        $interval->invert = $hours < 0 || $minutes < 0;

        return $interval;
    }

    private function resolveStartDate(string $timestamp, DateInterval $offset): DateTime
    {
        $start = DateTime::createFromFormat('U', $timestamp, new DateTimeZone('UTC'))
            ->setTimezone($this->timeZone);

        return DateTime::createFromFormat(
            'Y-m-d\TH:i:s',
            $start->format('Y-m-d\TH:i:s')
        )->add($offset);
    }
}
