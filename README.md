# Build calendars in the iCalendar format

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/Calendar.svg?style=flat-square)](https://packagist.org/packages/spatie/icalendar-generator)
[![Build Status](https://img.shields.io/travis/spatie/Calendar/master.svg?style=flat-square)](https://travis-ci.org/spatie/icalendar-generator)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/Calendar.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/icalendar-generator)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/Calendar.svg?style=flat-square)](https://packagist.org/packages/spatie/icalendar-generator)


With this package you can generate calendars for applications like Apple's Calendar and Google Calendar.
These calendars are generated in the iCalendar format and can be loaded by application that have support for iCalendar(RFC 5545).
This package tries to implement a minimal version of  [RFC 5545](https://tools.ietf.org/html/rfc5545) for a straightforward api.

## Example
```php
use Spatie\Calendar\Components\Calendar;
use Spatie\Calendar\Components\Event;

Calendar::new('My new fantastic calendar')
    ->event(Event::new('My new fantastic event')
        ->starts(new DateTime('16 may 2019'))
        ->ends(new DateTime('17 may 2019'))
    )->get();
```

## Installation

You can install the package via composer:

```bash
composer require spatie/Calendar
```

## Usage

### Calendars
You can create a calendar as such, it is not required to pass in a name
``` php
$calendar = Calendar::new('My new fantastic calendar');
```
A calendar can have a description
``` php
$calendar = Calendar::new()
    ->name('My new fantastic calendar')
    ->description('With the best events arround town');
```
You can get a textual version of the calendar as follows
``` php
Calendar::new('My new fantastic calendar')->get(); // BEGIN:VCALENDAR ...
```
There are multiple ways to add an event
``` php
// By parameter
$event = Event::new('Something great');

Calendar::new('My new fantastic calendar')
    ->event($event)
    ...
    
// By closure
Calendar::new('My new fantastic calendar')
    ->event(function(Event $event){
        $event->name('Something great');
    })
    ...
    
// By array
Calendar::new('My new fantastic calendar')
    ->event([
        Event::new('Something great'),
        Event::new('Another great event'),
    ])
    ...
```

### Event
An event can be created, a name is not required, but a start date should always be given
``` php
Event::new('My awesome event')
    ->starts(new DateTime('16 may 2019'));
```

You can set following properties on a event
``` php
Event::new()
    ->name('My awesome event')
    ->description('An awesome event with awesome activities')
    ->uniqueIdentifier('A unique identifier can be set here')
    ->location('Antwerp')
    ->created(new DateTime('10 may 2019'))
    ->starts(new DateTime('16 may 2019'))
    ->ends(new DateTime('17 may 2019'));
```
### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Credits

- [Ruben Van Assche](https://github.com/rubenvanassche)
- [All Contributors](../../contributors)

## Support us

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

Does your business depend on our contributions? Reach out and support us on [Patreon](https://www.patreon.com/spatie). 
All pledges will be dedicated to allocating workforce on maintenance and new awesome stuff.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
