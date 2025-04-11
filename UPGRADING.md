# Upgrading

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
