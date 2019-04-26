# Generate calendars in the iCalendar format

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/icalendar-generator.svg?style=flat-square)](https://packagist.org/packages/spatie/icalendar-generator)
[![Build Status](https://img.shields.io/travis/spatie/icalendar-generator/master.svg?style=flat-square)](https://travis-ci.org/spatie/icalendar-generator)
[![StyleCI](https://github.styleci.io/repos/170831958/shield?branch=master)](https://github.styleci.io/repos/170831958)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/icalendar-generator.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/icalendar-generator)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/icalendar-generator.svg?style=flat-square)](https://packagist.org/packages/spatie/icalendar-generator)

Using this package, you can generate calendars for applications like Apple's Calendar and Google Calendar.
Calendars will be generated in the iCalendar format (RFC 5545) which is a textual format that can be loaded by different applications.
This package tries to implement a minimal version of  [RFC 5545](https://tools.ietf.org/html/rfc5545) with some extensions from [RFC 7986](https://tools.ietf.org/html/rfc7986).
It's not our intention to implement these RFC's entirely but to provide a straightforward API that's easy to use.

Here's an example of how to use it:

```php
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;

Calendar::create('Laracon online')
    ->event(Event::create('Creating calender feeds')
        ->startsAt(new DateTime('6 March 2019 15:00'))
        ->endsAt(new DateTime('6 March 2019 16:00'))
    )
    ->get();
```

The above code will generate this string:

```
BEGIN:VCALENDAR
VERSION:2.0
PRODID:spatie/icalendar-generator
NAME:Laracon online
X-WR-CALNAME:Laracon online
BEGIN:VEVENT
UID:5cb9d22a00ba6
SUMMARY:Creating calender feeds
DTSTART:20190306T150000
DTEND:20190306T160000
DTSTAMP:20190419T135034
END:VEVENT
END:VCALENDAR
```

## Installation

You can install the package via composer:

```bash
composer require spatie/icalendar-generator
```

## Usage

Here's how you can create a calendar:

``` php
$calendar = Calendar::create();
```

You can give a name to a calendar:

``` php
$calendar = Calendar::create('Laracon Online');
```

A description can be added to an calendar:

``` php
$calendar = Calendar::create()
    ->name('Laracon Online')
    ->description('Experience Laracon all around the world');
```

There are multiple ways to add an event:

``` php
// As single event parameter
$event = Event::create('Creating calender feeds');

Calendar::create('Laracon Online')
    ->event($event)
    ...

// As an array of events
Calendar::create('Laracon Online')
    ->event([
        Event::create('Creating calender feeds'),
        Event::create('Creating contact lists'),
    ])
    ...    
    
// As a closure
Calendar::create('Laracon Online')
    ->event(function(Event $event){
        $event->name('Creating calender feeds');
    })
    ...
```

Here's how you can convert the calendar to text:

``` php
Calendar::create('Laracon Online')->get(); // BEGIN:VCALENDAR ...
```

When [streaming](#use-with-laravel) a calendar to an application, it is possible to set the refresh interval for the calendar by duration in minutes.
When setting this, the calendar application will check your server every time after the specified duration for changes to the calendar.

``` php
Calendar::create('Laracon Online')
    ->refreshInterval(5)
    ...
```

### Event

An event can be created as follows. A name is not required, but a start date should always be given.

``` php
Event::create('Laracon Online')
    ->startsAt(new DateTime('6 march 2019'));
```

You can set following properties on an event:

``` php
Event::create()
    ->name('Laracon Online')
    ->description('Experience Laracon all around the world')
    ->uniqueIdentifier('A unique identifier can be set here')
    ->location('Antwerp')
    ->createdAt(new DateTime('6 march 2019'))
    ->startsAt(new DateTime('6 march 2019 15:00'))
    ->endsAt(new DateTime('6 march 2019 16:00'));
```

Want to create an event quickly with start and end date?

``` php
Event::create('Laracon Online')
    ->period(new DateTime('6 march 2019'), new DateTime('7 march 2019'));
```

#### Using Carbon

Since this package expects a DateTimeInterface for properties related to date and time, it is possible to use the popular [Carbon library](https://carbon.nesbot.com/).

``` php
use Carbon\Carbon;

Event::create('Laracon Online')
    ->startsAt(Carbon::now())
    ...
```

#### Timezones

By default events will not use timezones, this means an event like noon at 12 o'clock will be shown for someone in New York
at a different time than for someone in Sydney.

If you want to show an event at the exact time it is happening, for example, a talk at an online conference streamed around the world. Then you should consider using timezones.

This package relies on the timezones provided by [PHP DateTime](https://www.php.net/manual/en/datetime.settimezone.php) if you want to include these timezones in an event you can do the following.

``` php
$start = new DateTime('6 march 2019 15:00', new DateTimeZone('Europe/Brussels'))

Event::create()
    ->startsAt($starts)
    ->withTimezones()
    ...
```

Want timezones in each event of the calendar?

``` php
Calendar::create()
   ->withTimezones()
    ....
```

#### Alerts

Alerts allow calendar clients to send reminders about specific events. For example, Apple Mail on an iPhone will send users a notification about the event.
An alert always belongs to an event and has a description and the number of minutes before the event when it is triggered. 


``` php
Event::create('Laracon Online')
    ->alertMinutesBefore(5, 'Laracon online is going to start in five mintutes')
```

### Use with Laravel

You can use Laravel Responses to stream to calendar applications

``` php
use Illuminate\Http\Response;

$calendar = Calendar::create('Laracon Online');

Response::create($calendar->get())
   ->headers([
      'Content-Type:text/calendar;charset=utf-8',
   ]);
```

If you want to add the possibility for users to download a calendar and import it into a calendar application

``` php
use Illuminate\Http\Response;

$calendar = Calendar::create('Laracon Online');

Response::create($calendar->get())
   ->headers([
      'Content-Type:text/calendar;charset=utf-8',
   ])
   ->download('my-awesome-calendar.ics');
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
