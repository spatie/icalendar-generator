# Upgrading

## Upgrading from 3.1 to 3.2

### Full day events no longer get an extra day added

Versions 2.6.2 up to and including 3.1.1 added one day to the end date of a full day event. Version 3.2.0 removed that behaviour. The package now writes the end date exactly as you provide it.

The iCalendar spec defines the end of an event as non inclusive, which means the end date is the day after the last day of the event. You are now responsible for that conversion yourself.

If you wrote code against 2.6.2 up to and including 3.1.1, add one day to every end date you pass to a full day event:

``` php
// Before 3.2.0, the package added the day for you
Event::create()
    ->fullDay()
    ->startsAt(new DateTime('6 March 2019'))
    ->endsAt(new DateTime('6 March 2019'));

// From 3.2.0 on, you add the day yourself
Event::create()
    ->fullDay()
    ->startsAt(new DateTime('6 March 2019'))
    ->endsAt(new DateTime('7 March 2019'));
```

Both of these produce the same output:

```
DTSTART;VALUE=DATE:20190306
DTEND;VALUE=DATE:20190307
```

If you skip this change, every full day event ends one day too early. Calendar applications will not report an error, they simply display a shorter event.

Two things worth knowing:

* Events that use `fullDay()` are the only ones affected. Events with a time were always written unchanged.
* If you upgraded from a version older than 2.6.2 you are not affected, because those versions also wrote the end date unchanged.

## Upgrading from 2.x to 3.x

Version 3 has some minor changes:

### Enums

We removed the dependency on spatie/enum and opted for PHP native enums. This means that you should update all the enums in your code belonging to the package:

- `Spatie\IcalendarGenerator\Enums\Classification`
- `Spatie\IcalendarGenerator\Enums\Display`
- `Spatie\IcalendarGenerator\Enums\EventStatus`
- `Spatie\IcalendarGenerator\Enums\ParticipationStatus`
- `Spatie\IcalendarGenerator\Enums\RecurrenceDay`
- `Spatie\IcalendarGenerator\Enums\RecurrenceFrequency`
- `Spatie\IcalendarGenerator\Enums\RecurrenceMonth`
- `Spatie\IcalendarGenerator\Enums\TimezoneEntryType`

Like so:

```php
RecurrenceMonth::january(); // old

RecurrenceMonth::January; // new
```

### Payloads

If you were building your own payloads, please notice that the `optional` and `multiple` methods have been removed.

## Upgrading from 1.x to 2.x

Version 2.0 adds some breaking changes:

- PHP 7.4|8.0 only
- Timezones are now opt-out instead of opt-in, which makes that each date property will have a timezone
- Property types are now simply called properties
- Property only accepts a `string` as name, you can add aliases via the `addAlias` function
- ComponentPayload has removed `textProperty`, `dateTimeProperty` and `when` in favour of `property`, `optional` and `multiple`
