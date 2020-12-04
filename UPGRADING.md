# Upgrading

Version 2.0 adds some breaking changes:

- PHP 7.4 only
- Property types are now simple called properties
- Property only accept a `string` as name, you can add aliases via the `addAlias` function
- ComponentPayload has removed `textProperty`, `dateTimeProperty` and `when` in favour of `property` and `optional`
