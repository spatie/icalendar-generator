# Changelog

All notable changes to `icalendar-generator` will be documented in this file

## 2.6.0 - 2024-02-14

- Carbon is not mandatory, we can use php DateTime objects (#115)

## 2.5.6 - 2023-03-24

- Fix bugs introduced with previous pr

## 2.5.5 - 2023-03-10

- Allow adding rrules as string (#110)

## 2.5.4 - 2023-01-25

### What's Changed

- Fix PHP 7.4 and test issues by @htto in https://github.com/spatie/icalendar-generator/pull/106

### New Contributors

- @htto made their first contribution in https://github.com/spatie/icalendar-generator/pull/106

**Full Changelog**: https://github.com/spatie/icalendar-generator/compare/2.5.3...2.5.4

## 2.5.3 - 2023-01-24

- fix syntax error

**Full Changelog**: https://github.com/spatie/icalendar-generator/compare/2.5.2...2.5.3

## 2.5.2 - 2023-01-23

- Add embedded attachments to Events (#104)

## 2.5.1 - 2022-10-26

### What's Changed

- Refactor all tests to PEST by @alexmanase in https://github.com/spatie/icalendar-generator/pull/100
- Fix GEO coordinates on locale with comma as decimal separator by @cweiske in https://github.com/spatie/icalendar-generator/pull/99

### New Contributors

- @alexmanase made their first contribution in https://github.com/spatie/icalendar-generator/pull/100

**Full Changelog**: https://github.com/spatie/icalendar-generator/compare/2.5.0...2.5.1

## 2.5.0 - 2022-10-10

### What's Changed

- Add google meet and microsoft teams to event by @MammutAlex in https://github.com/spatie/icalendar-generator/pull/98

### New Contributors

- @MammutAlex made their first contribution in https://github.com/spatie/icalendar-generator/pull/98

**Full Changelog**: https://github.com/spatie/icalendar-generator/compare/2.4.0...2.5.0

## 2.4.0 - 2022-09-08

### What's Changed

- `then` -> `than` by @edalzell in https://github.com/spatie/icalendar-generator/pull/85
- Add calendar source parameter by @cweiske in https://github.com/spatie/icalendar-generator/pull/95

**Full Changelog**: https://github.com/spatie/icalendar-generator/compare/2.3.3...2.4.0

## 2.3.3 - 2022-02-10

- Use UTC timezone for default DTSTAMP value (#78)

## 2.3.2 - 2020-11-25

- add support for adding images to an event (#71)

## 2.3.1 - 2020-11-02

- add support for requires RSVP on attendees (#67)
- fix an issue in the timezone time range calculation where the max value would always be today

## 2.3.0 - 2020-08-20

- add support for attachments on events
- add a new expectation testing mechanism for internal tests

## 2.2.2 - 2020-08-19

- fix date timezones on all day events (#55)

## 2.2.1 - 2020-07-08

- fix coordinates with Apple calendar (#51)

## 2.2.0 - 2020-06-08

- follow the RFC more exactly with full days
- add support for multiple value types within parameters
- add an `EmptyProperty` without value and only parameters

## 2.1.1 - 2020-03-03

- allow a full day event without specifying the end date

## 2.1.0 - 2020-02-19

- add the use 'Z' within UTC timestamps and remove the TZID parameter

## 2.0.2 - 2020-01-07

- fix positive timezone offsets

## 2.0.1 - 2020-01-07

- fix case when getTransitions() returns a false value (#38)

## 2.0.0 - 2020-11-26

- add support for timezones with automatically generated components
- add `Timezone` and `TimezoneEntry` components
- add support for basic RRULE's
- add support for repeating events on specific dates
- add support for not repeating events on specific dates
- add valueObjects for most used types
- remove `final` classes
- remove support for < PHP 7.4
- add Psalm typechecking

## 1.0.6 - 2020-11-26

- add support for PHP 8.0

## 1.0.5 - 2020-10-09

- add support for url's for events (#31)

## 1.0.4 - 2020-09-25

- fix timezones on older clients (#30)

## 1.0.3 - 2020-04-29

- fix addresses not working without the name of the address

## 1.0.2 - 2020-03-27

- add escaping in property parameters (#17)

## 1.0.1 - 2020-03-25

- add more conformity to RFC5545 (#13, #14)

## 1.0.0 - 2020-01-17

- initial release
