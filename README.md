# Generate calendars in the iCalendar format

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/icalendar-generator.svg?style=flat-square)](https://packagist.org/packages/spatie/icalendar-generator)
[![Build Status](https://img.shields.io/github/workflow/status/spatie/icalendar-generator/Test?label=Tests)](https://github.com/spatie/icalendar-generator/actions?query=workflow%3ATest)
[![Style](https://img.shields.io/github/workflow/status/spatie/icalendar-generator/Check%20&%20fix%20styling?label=Style)](https://github.com/spatie/icalendar-generator/actions?query=workflow%3A%22Check+%26+fix+styling%22)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/icalendar-generator.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/icalendar-generator)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/icalendar-generator.svg?style=flat-square)](https://packagist.org/packages/spatie/icalendar-generator)

Want to create online calendars, so you can display them on an iPhone's calendar app or in Google Calendar? 
This can be done by generating calendars in the iCalendar format (RFC 5545), which is a textual format that can be loaded by different applications.
The format of such calendars is defined in [RFC 5545](https://tools.ietf.org/html/rfc5545) which is not a pleasant reading experience.
This package implements [RFC 5545](https://tools.ietf.org/html/rfc5545) and some extensions from [RFC 7986](https://tools.ietf.org/html/rfc7986) to provide you an easy to use API for creating calendars.
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
UID:5ef5c3f64cb2c
DTSTAMP;TZID=UTC:20200626T094630
SUMMARY:Creating calender feeds
DTSTART;TZID=UTC:20190306T150000
DTEND;TZID=UTC:20190306T160000
END:VEVENT
END:VCALENDAR
```

## Support us

Learn how to create a package like this one, by watching our premium video course:

[![Laravel Package training](https://spatie.be/github/package-training.jpg)](https://laravelpackage.training)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require spatie/icalendar-generator
```

## Upgrading

There were some substantial changes between v1 and v2 of the package, check the [upgrade](https://github.com/spatie/icalendar-generator/blob/master/UPGRADING.md) guide for more information.

## Usage

Here's how you can create a calendar:

``` php
$calendar = Calendar::create();
```

You can give a calendar a name:

``` php
$calendar = Calendar::create('Laracon Online');
```

A description can be added to a calendar:

``` php
$calendar = Calendar::create()
    ->name('Laracon Online')
    ->description('Experience Laracon all around the world');
```

In the end, you want to convert your calendar to text, so it can be streamed or downloaded to the user. Here's how you do that:

``` php
Calendar::create('Laracon Online')->get(); // BEGIN:VCALENDAR ...
```

When [streaming](#use-with-laravel) a calendar to an application, it is possible to set the refresh interval for the calendar by duration in minutes. When setting this, the calendar application will check your server every time after the specified duration for changes to the calendar:

``` php
Calendar::create('Laracon Online')
    ->refreshInterval(5)
    ...
```

### Event

An event can be created as follows. A name is not required, but a start date should always be given:

``` php
Event::create('Laracon Online')
    ->startsAt(new DateTime('6 march 2019'));
```

You can set the following properties on an event:

``` php
Event::create()
    ->name('Laracon Online')
    ->description('Experience Laracon all around the world')
    ->uniqueIdentifier('A unique identifier can be set here')
    ->createdAt(new DateTime('6 march 2019'))
    ->startsAt(new DateTime('6 march 2019 15:00'))
    ->endsAt(new DateTime('6 march 2019 16:00'));
```

Want to create an event quickly with start and end date?

``` php
Event::create('Laracon Online')
    ->period(new DateTime('6 march 2019'), new DateTime('7 march 2019'));
```

You can add a location to an event a such:

``` php
Event::create()
    ->address('Samberstraat 69D, 2060 Antwerp, Belgium')
    ->addressName('Spatie HQ')
    ->coordinates(51.2343, 4.4287)
    ...
```

You can set the organizer of an event, the email address is required, but the name can be omitted:

``` php
Event::create()
    ->organizer('ruben@spatie.be', 'Ruben')
    ...
```

Attendees of an event can be added as such

``` php
Event::create()
    ->attendee('ruben@spatie.be') // only an email address is required
    ->attendee('brent@spatie.be', 'Brent')
    ...
```

You can also set the participation status of an attendee:

``` php
Event::create()
    ->attendee('ruben@spatie.be', 'Ruben', ParticipationStatus::accepted())
    ...
```

There are three participation statuses:

- `ParticipationStatus::accepted()`
- `ParticipationStatus::declined()`
- `ParticipationStatus::tentative()`

An event can be made transparent, so it does not overlap visually with other events in a calendar:

``` php
Event::create()
    ->transparent()
    ...
```

It is possible to create an event that spans a full day:

``` php
Event::create()
    ->fullDay()
    ...
```

The status of an event can be set:

``` php
Event::create()
    ->status(EventStatus::cancelled())
    ...
```

There are three event statuses:

- `EventStatus::confirmed()`
- `EventStatus::cancelled()`
- `EventStatus::tentative()`

An event can be classified(`public`, `private`, `confidential`) as such:

``` php
Event::create()
    ->classification(Classification::private())
    ...
```

After creating your event, it should be added to a calendar. There are multiple options to do this:

``` php
// As a single event parameter
$event = Event::create('Creating calendar feeds');

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

#### Using Carbon

Since this package expects a DateTimeInterface for properties related to date and time, it is possible to use the popular [Carbon library](https://carbon.nesbot.com/):

``` php
use Carbon\Carbon;

Event::create('Laracon Online')
    ->startsAt(Carbon::now())
    ...
```

#### Timezones

Events will use the timezones defined in your `DateTime`, these timezones are always [set]((https://www.php.net/manual/en/datetime.settimezone.php)) by PHP in a `DateTime` object.
It is possible to change this default timezone, you can find more information: [here](https://www.php.net/manual/en/function.date-default-timezone-set.php).

Just a reminder, do not use PHP's `setTimezone` function on a `DateTime` object, it will change the time according to the timezone! It is better to create a new `DateTime` object with a timezone as such:

``` php
new DateTime('6 march 2019 15:00', new DateTimeZone('Europe/Brussels'))
```

A point can be made for omitting timezones. This can happen in the case where you want to show an event at noon in the world. We define noon at 12 o'clock but that time is actually relative. It not the same for people in Belgium or Australia or any other country in the world.

That's why you can disable timezones on events:

``` php
$starts = new DateTime('6 march 2019 15:00', new DateTimeZone('Europe/Brussels'))

Event::create()
    ->startsAt($starts)
    ->withoutTimezone()
    ...
```

Or you could even disable timezones for a whole calendar:

``` php
Calendar::create()
   ->withoutTimezone()
    ....
```

#### Alerts

Alerts allow calendar clients to send reminders about specific events. For example, Apple Mail on an iPhone will send users a notification about the event. An alert always belongs to an event and has a description and the number of minutes before the event it will be triggered:


``` php
Event::create('Laracon Online')
    ->alertMinutesBefore(5, 'Laracon online is going to start in five mintutes');
```

You can also trigger an alert after the event:

``` php
Event::create('Laracon Online')
    ->alertMinutesAfter(5, 'Laracon online has ended, see you next year!');
```

Or trigger an alert on a specific date:

``` php
Event::create('Laracon Online')
    ->alert(Alert::date(
        new DateTime('05/16/2020 12:00:00'),
        'Laracon online has ended, see you next year!'
    ))
```

### Use with Laravel

You can use Laravel Responses to stream to calendar applications:

``` php
$calendar = Calendar::create('Laracon Online');

response($calendar->get())
    ->header('Content-Type', 'text/calendar')
    ->header('charset', 'utf-8');
```

If you want to add the possibility for users to download a calendar and import it into a calendar application:

``` php
$calendar = Calendar::create('Laracon Online');

response($calendar->get())
    ->header('Content-Type', 'text/calendar')
    ->header('charset', 'utf-8')
    ->download('my-awesome-calendar.ics');
```

### Extending the package

We try to keep this package as straightforward as possible. That's why a lot of properties and subcomponents from the RFC are not included in this package. We've made it possible to add other properties or subcomponents to each component in case you might need something not included in the package. But be careful! From this moment, you're on your own correctly implementing the RFC's.

#### Appending properties

You can add a new property to a component like this:

```php
Calendar::create()
    ->appendProperty(
        TextPropertyType::create('ORGANIZER', 'ruben@spatie.be')
    )
    ...
```

Here we've added a `TextPropertyType`, and this is a default key-value property type with a text as value. You can also use the `DateTimePropertyType`, the `DurationPropertyType` or create your own by extending the `PropertyType` class.

Sometimes a property can have some additional parameters, these are key-value entries and can be added to properties as such:

```php
$property = TextPropertyType::create('ORGANIZER', 'ruben@spatie.be')
    ->addParameter(Parameter::create('CN', 'RUBEN VAN ASSCHE'));

Calendar::create()
    ->appendProperty($property)
    ...
```

#### Appending subcomponents


A subcomponent can be appended as such:

```php
Calendar::create()
    ->appendSubComponent(
        Event::create('Extending icalendar-generator')
    )
    ...
```

It is possible to create your subcomponents by extending the `Component` class.

### Testing

``` bash
composer test
```

### Alternatives

We strive for a simple and easy to use API, want something more? Then check out this [package](https://github.com/markuspoerschke/iCal) by markus poerschke.

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

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
