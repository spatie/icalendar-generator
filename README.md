# Build calendars in the iCalendar format

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/Calendar.svg?style=flat-square)](https://packagist.org/packages/spatie/icalendar-generator)
[![Build Status](https://img.shields.io/travis/spatie/Calendar/master.svg?style=flat-square)](https://travis-ci.org/spatie/icalendar-generator)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/Calendar.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/icalendar-generator)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/Calendar.svg?style=flat-square)](https://packagist.org/packages/spatie/icalendar-generator)


With this package, you can generate calendars for applications like Apple's Calendar and Google Calendar.
Calendars will be generated in the iCalendar format (RFC 5545) which is a textual format that can be loaded by different applications.
This package tries to implement a minimal version of  [RFC 5545](https://tools.ietf.org/html/rfc5545) with some extensions from [RFC 7986](https://tools.ietf.org/html/rfc7986).
It's not our intention to implement these RFC's entirely but to provide a straightforward API that's easy to use.

## Example
```php
use Spatie\Calendar\Components\Calendar;
use Spatie\Calendar\Components\Event;

Calendar::new('Laracon online')
    ->event(Event::new('Creating calender feeds by Ruben Van Assche')
        ->starts(new DateTime('6 March 2019 15:00'))
        ->ends(new DateTime('6 March 2019 16:00'))
    )->stream();
```

## Installation

You can install the package via composer:

```bash
composer require spatie/Calendar
```

## Usage

### Calendars

Here's how you can create a calendar. 

``` php
$calendar = Calendar::new();
```

You can also give a name to a calendar

``` php
$calendar = Calendar::new('Laracon Online');
```

A description can be added to an calendar.

``` php
$calendar = Calendar::new()
    ->name('Laracon Online')
    ->description('Experience Laracon all around the world');
```

There are multiple ways to add an event.

``` php
// As single event parameter
$event = Event::new('Creating calender feeds by Ruben Van Assche');

Calendar::new('Laracon Online')
    ->event($event)
    ...

// As an array of events
Calendar::new('Laracon Online')
    ->event([
        Event::new('Creating calender feeds by Ruben Van Assche'),
        Event::new('Websockets by Marcel Pociot'),
    ])
    ...    
    
// As a closure
Calendar::new('Laracon Online')
    ->event(function(Event $event){
        $event->name('Creating calender feeds by Ruben Van Assche');
    })
    ...
```

Here's how you can convert the calendar to text.

``` php
Calendar::new('Laracon Online')->get(); // BEGIN:VCALENDAR ...
```

Streaming the calendar to clients like Apple Mail over the https protocol can be done as follows.

``` php
Calendar::new('Laracon Online')->stream();
```

When streaming a calendar, it is possible to set the refresh interval for the calendar by a [duration](#Durations). 
The calendar client application will always check your server after the specified duration for changes in the calendar.

``` php
Calendar::new('Laracon Online')
    ->refreshInterval(Duration::new()->minutes(5))
    ->stream();
```

If you want to add the possibility for users to download a calendar and import it into an application

``` php
Calendar::new('Laracon Online')->download();
```

### Event

An event can be created as follows. A name is not required, but a start date should always be given.

``` php
Event::new('Laracon Online')
    ->starts(new DateTime('6 march 2019'));
```

You can set following properties on an event

``` php
Event::new()
    ->name('Laracon Online')
    ->description('Experience Laracon all around the world')
    ->uniqueIdentifier('A unique identifier can be set here')
    ->location('Antwerp')
    ->created(new DateTime('6 march 2019'))
    ->starts(new DateTime('6 march 2019 15:00))
    ->ends(new DateTime('6 march 2019 16:00'));
```

#### Using Carbon

Since this package expects a DateTimeInterface for properties related to date and time, it is possible to use the popular [Carbon library](https://carbon.nesbot.com/).

``` php
use Carbon\Carbon;

Event::new('Laracon Online')
    ->starts(Carbon::now())
    ...
```

#### Timezones

By default events will not use timezones, this means an event like noon at 12 o'clock will be shown for someone in New York
at a different time then for someone in Sydney.

If you want to show an event at the exact time it is happening, for example, a talk at a conference then you should consider using timezones.

This package relies on the timezones provided by [PHP DateTime](https://www.php.net/manual/en/datetime.settimezone.php) if you want to include these timezones in an event you can do the following.

``` php
$start = new DateTime('6 march 2019 15:00', new DateTimeZone('Europe/Brussels'))

Event::new()
    ->starts($starts)
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

Alarms allow calendar clients to send reminders about certain events. For example, Apple Mail on an iPhone will send users a push notification about the event.

``` php
Alarm::new('Event Sourcing by Freek Murze is starting soon');
```

An alarm always has a description and should be added to an event. It is possible to add multiple alarms to one event.
 
Adding an alarm to an event can be done as such:

``` php
$alarm = Alarm::new('Creating calender feeds by Ruben Van Assche');

Calendar::event('Event Sourcing by Freek Murze')
    ->alarm($alarm)
    ...

// As an array of events
Calendar::event('Creating calender feeds by Ruben Van Assche')
    ->alarm([
        Alarm::new('The talk by Ruben is staring soon'),
        Event::new('The talk by Ruben is staring realy soon'),
    ])
    ...    
    
// As a closure
Calendar::event('Creating calender feeds by Ruben Van Assche')
    ->alarm(function(Alarm $alarm){
        $alarm->name('The talk by ruben is staring soon');
    })
    ...
```

There are three possible approaches to trigger an alarm

``` php
// At a specified date timestamp

Alarm::new('The talk by Ruben is starting soon')
    ->triggerAt(new DateTime('6 march 2019 14:55));
    
// A period after the starting of the event
$duration = Duration::new()->minutes(5);

Alarm::new('The talk by Ruben is starting soon')
    ->triggerBeforeEvent($duration);
    
// A period before the starting of the event
$duration = Duration::new()->minutes(5)->backInTime()

Alarm::new('The talk by Ruben has ended')
    ->triggerAfterEvent($duration);
```

In the last example we give a negative duration hence: `->backInTime()`, without this negative duration the alarm would trigger 5 minutes after the event had started.
Want to trigger an alarm 5 minutes before the event has ended, then use `->backInTime()` with `triggerBeforeEvent`.

An alarm can be repeated for a number of times after an interval, by default an alarm will be repeated once

``` php
Alarm::new('The talk by Ruben is starting soon')
    ->triggerAt(new DateTime('6 march 2019 14:55));
    ->repeat(Duration::new()->minutes(2))

// Repeating multiple times
Alarm::new('The talk by Ruben is starting soon')
    ->triggerAt(new DateTime('6 march 2019 14:55));
    ->repeat(Duration::new()->minutes(1), 5)
```

### Durations

A duration can be constructed like this:

``` php
Duration::new()
    ->weeks(3)
    ->days(1)
    ->hours(4)
    ->minutes(1)
    ->seconds(5)
```

Durations can also be negative.

``` php
Duration::new()
    ->backInTime()
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
