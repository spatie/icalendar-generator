<?php

namespace Spatie\IcalendarGenerator\Timezones;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use Spatie\IcalendarGenerator\Enums\TimezoneEntryType;

class TimezoneTransitionsResolver
{
    protected DateTimeImmutable $start;

    protected DateTimeImmutable $end;

    public function __construct(
        protected DateTimeZone $timeZone,
        DateTimeInterface $start,
        DateTimeInterface $end
    ) {
        $this->start = (new DateTimeImmutable($start->format(DATE_ATOM)))->sub(
            new DateInterval('P270D'),
        );
        $this->end = (new DateTimeImmutable($end->format(DATE_ATOM)))->add(
            new DateInterval('P185D')
        );
    }

    /**
     * @return TimezoneTransition[]
     */
    public function getTransitions(): array
    {
        $transitions = $this->timeZone->getTransitions(
            $this->start->getTimestamp(),
            $this->end->getTimestamp(),
        );

        if ($transitions === false) {
            // for timezone '+00:00' DateTimeZone::getTransitions() returns boolean false,
            // so check for that first and create the fake entry to make it work
            $transitions = [
                [
                    'isdst' => false,
                    'offset' => 0,
                    'ts' => $this->start->getTimestamp(),
                    'abbr' => 'UTC',
                ],
            ];
        }

        if (count($transitions) === 1) {
            // Add a fake transition for UTC for example which does not have transitions
            $transitions[] = [
                'isdst' => $transitions[0]["isdst"],
                'offset' => $transitions[0]["offset"],
                'ts' => $this->start->getTimestamp(),
                'abbr' => $transitions[0]["abbr"],
            ];
        }

        $found = [];
        $previousTransition = $transitions[0];

        // Skip the first to determine the offset
        for ($i = 1; $i < count($transitions); $i++) {
            $transition = $transitions[$i];

            $type = $transition['isdst']
                ? TimezoneEntryType::Daylight
                : TimezoneEntryType::Standard;

            $offsetFrom = $this->resolveOffset($previousTransition['offset']);
            $offsetTo = $this->resolveOffset($transition['offset']);

            $offsetDiff = $this->resolveOffsetDiff($offsetFrom, $offsetTo);

            $found[] = new TimezoneTransition(
                $this->resolveStartDate((string) $transition['ts'], $offsetDiff),
                $offsetFrom,
                $offsetTo,
                $type
            );

            $previousTransition = $transition;
        }

        return $found;
    }

    protected function resolveOffset(int $offset): DateInterval
    {
        $hours = (int) floor($offset / 3600);
        $minutes = abs(intdiv($offset, 60) % 60);

        return $this->resolveInterval($hours, $minutes);
    }

    protected function resolveOffsetDiff(DateInterval $from, DateInterval $to): DateInterval
    {
        $hours = (int) $from->format('%r%h') - (int) $to->format('%r%h');
        $minutes = (int) $from->format('%r%m') - (int) $to->format('%r%m');

        return $this->resolveInterval($hours, $minutes);
    }

    protected function resolveInterval(int $hours, int $minutes): DateInterval
    {
        $interval = new DateInterval(
            'PT'.abs($hours).'H'.abs($minutes).'M'
        );

        if ($hours < 0 || $minutes < 0) {
            $interval->invert = 1;
        }

        return $interval;
    }

    protected function resolveStartDate(string $timestamp, DateInterval $offset): DateTime
    {
        $start = DateTime::createFromFormat('U', $timestamp, new DateTimeZone('UTC'));

        if ($start === false) {
            throw new Exception('Could not create DateTime from timestamp');
        }

        $normalizedStart = DateTime::createFromFormat(
            'Y-m-d\TH:i:s',
            $start->setTimezone($this->timeZone)->format('Y-m-d\TH:i:s')
        );

        if ($normalizedStart === false) {
            throw new Exception('Could not create DateTime from timestamp');
        }

        return $normalizedStart->add($offset);
    }
}
