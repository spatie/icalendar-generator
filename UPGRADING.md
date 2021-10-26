# Upgrading

Version 2.0 adds some breaking changes:

- PHP 7.4|8.0 only
- Timezones are now opt-out instead of opt-in, which makes that each date property will have a timezone
- Property types are now simply called properties
- Property only accepts a `string` as name, you can add aliases via the `addAlias` function
- ComponentPayload has removed `textProperty`, `dateTimeProperty` and `when` in favour of `property`, `optional` and `multiple`
