# Generate calendars in the iCalendar format

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/icalendar-generator.svg?style=flat-square)](https://packagist.org/packages/spatie/icalendar-generator)
[![Build Status](https://img.shields.io/github/workflow/status/spatie/icalendar-generator/Test?label=Tests)](https://github.com/spatie/icalendar-generator/actions?query=workflow%3ATest)
[![Style](https://img.shields.io/github/workflow/status/spatie/icalendar-generator/Check%20&%20fix%20styling?label=Style)](https://github.com/spatie/icalendar-generator/actions?query=workflow%3A%22Check+%26+fix+styling%22)
[![Psalm](https://img.shields.io/github/workflow/status/spatie/icalendar-generator/Psalm?label=Psalm)](https://github.com/spatie/icalendar-generator/actions?query=workflow%3APsalm)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/icalendar-generator.svg?style=flat-square)](https://packagist.org/packages/spatie/icalendar-generator)

Using this package, you can generate calendars for applications like Apple's Calendar and Google Calendar.
Calendars will be generated in the iCalendar format (RFC 5545), which is a textual format that can be loaded by different applications.
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
DTSTART:20190306T150000Z
DTEND:20190306T160000Z
DTSTAMP:20190419T135034Z
END:VEVENT
END:VCALENDAR
```

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/icalendar-generator.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/icalendar-generator)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

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

In the end, you want to convert your calendar to text so it can be streamed or downloaded to the user. Here's how you do that:

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
    ->address('Kruikstraat 22, 2018 Antwerp, Belgium')
    ->addressName('Spatie HQ')
    ->coordinates(51.2343, 4.4287)
    ...
```

You can set the organizer of an event, the email address is required but the name can be omitted:

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

By default, events will not use timezones. This means an event like noon at 12 o'clock will be shown for someone in New York at a different time than for someone in Sydney.

If you want to show an event at the exact time it is happening, for example, a talk at an online conference streamed around the world. Then you should consider using timezones.

This package relies on the timezones provided by [PHP DateTime](https://www.php.net/manual/en/datetime.settimezone.php) if you want to include these timezones in an event you can do the following:

``` php
$starts = new DateTime('6 march 2019 15:00', new DateTimeZone('Europe/Brussels'))

Event::create()
    ->startsAt($starts)
    ->withTimezone()
    ...
```

Want timezones in each event of the calendar, then add `withTimezones` to your `Calendar`:

``` php
Calendar::create()
   ->withTimezone()
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

return response($calendar->get(), 200, [
   'Content-Type' => 'text/calendar',
   'Content-Disposition' => 'attachment; filename="my-awesome-calendar.ics"',
   'charset' => 'utf-8',
]);
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
