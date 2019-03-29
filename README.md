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
You can create a new calendar as follows, it is not required to, pass in a name

``` php
$calendar = Calendar::new('My new fantastic calendar');
```

A description can be added to an calendar

``` php
$calendar = Calendar::new()
    ->name('My new fantastic calendar')
    ->description('With the best events arround town');
```

At the the end of building the calendar you can get a textual representation. You should set the correct headers when returning the calendar as an http response, more info here.

``` php
Calendar::new('My new fantastic calendar')->get(); // BEGIN:VCALENDAR ...
```

There are multiple ways to add an event

``` php
// As single event parameter
$event = Event::new('Something great');

Calendar::new('My new fantastic calendar')
    ->event($event)
    ...

// As an array of events
Calendar::new('My new fantastic calendar')
    ->event([
        Event::new('Something great'),
        Event::new('Another great event'),
    ])
    ...    
    
// As a closure
Calendar::new('My new fantastic calendar')
    ->event(function(Event $event){
        $event->name('Something great');
    })
    ...
    

```

### Event
An event can be created as follows. A name is not required, but a start date should always be given

``` php
Event::new('My awesome event')
    ->starts(new DateTime('16 may 2019'));
```

You can set following properties on a event

``` php
Event::new()
    ->name('My awesome event')
    ->description('An awesome event with awesome activities')
    ->refreshInterval(Duration::new()->minutes(5))
    ->uniqueIdentifier('A unique identifier can be set here')
    ->location('Antwerp')
    ->created(new DateTime('10 may 2019'))
    ->starts(new DateTime('16 may 2019'))
    ->ends(new DateTime('17 may 2019'));
```

#### Timezones
By default your event will not use timezones, this will add the possibility for an event to happen at different times in different timezones. 
An example of this can be the possibility to add an event at noon.
It won't matter if someone is in New York or in Sydney opening a calendar app, there will be an event at twelve o'clock.

If you want to use timezones in your calendar, then you should add following to the event. The event will check the dates provided if a timezone is provided. Check out PHP's DateTime and DateTimeZone for more information or use a library like Carbon.

``` php
Event::new()
    ->withTimezones()
    ...
```

Want timezones in each event of the calendar?

``` php
Calendar::new()
    ->withTimezones()
    ....
```

### Alarm
Alarms allow calendars to send reminders about certain events. 
An alarm has a description that's required:

``` php
Alarm('The awesome event is staring');
```

There are 3 few ways to trigger an alert

``` php
// At a specified datetimestamp
Alarm('The awesome event is staring')
    ->triggerAt(new DateTime('16 may 2019'));
    
// A period before or after the starting of the event
$duration = Duration::new()->minutes(5)->backInTime()

Alarm('The awesome event is staring')
    ->triggerBeforeEvent(new DateTime('16 may 2019'));
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
