# Generate calendars in the iCalendar format

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/icalendar-generator.svg?style=flat-square)](https://packagist.org/packages/spatie/icalendar-generator)
[![Tests](https://github.com/spatie/icalendar-generator/actions/workflows/run-tests.yml/badge.svg)](https://github.com/spatie/icalendar-generator/actions/workflows/run-tests.yml)
[![Check & fix styling](https://github.com/spatie/icalendar-generator/actions/workflows/fix-styling.yml/badge.svg)](https://github.com/spatie/icalendar-generator/actions/workflows/fix-styling.yml)
[![PHPStan](https://github.com/spatie/icalendar-generator/actions/workflows/phpstan.yml/badge.svg)](https://github.com/spatie/icalendar-generator/actions/workflows/phpstan.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/icalendar-generator.svg?style=flat-square)](https://packagist.org/packages/spatie/icalendar-generator)

Want to create online calendars so that you can display them on an iPhone's calendar app or in Google Calendar?
This can be done by generating calendars in the iCalendar format (RFC 5545), a textual format that can be loaded by different applications.

The format of such calendars is defined in [RFC 5545](https://tools.ietf.org/html/rfc5545), which is not a pleasant reading experience.
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
SUMMARY:Creating calendar feeds
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

## Upgrading

There were some substantial changes between v1 and v2 of the package. Check the [upgrade](https://github.com/spatie/icalendar-generator/blob/master/UPGRADING.md) guide for more information.

## Usage

Here's how you can create a calendar:

``` php
$calendar = Calendar::create();
```

You can give a name to the calendar:

``` php
$calendar = Calendar::create('Laracon Online');
```

A description can be added to a calendar:

``` php
$calendar = Calendar::create()
    ->name('Laracon Online')
    ->description('Experience Laracon all around the world');
```

In the end, you want to convert your calendar to text so that it can be streamed or downloaded to the user. Here's how you do that:

``` php
Calendar::create('Laracon Online')->get(); // BEGIN:VCALENDAR ...
```

When [streaming](#use-with-laravel) a calendar to an application, it is possible to set the calendar's refresh interval by duration in minutes. When setting this, the calendar application will check your server every time after the specified duration for changes to the calendar:

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

Want to create an event quickly with a start and end date?

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

You can set the organizer of an event, the email address is required, but the name can be omitted:

``` php
Event::create()
    ->organizer('ruben@spatie.be', 'Ruben')
    ...
```

Attendees of an event can be added as such:

``` php
Event::create()
    ->attendee('ruben@spatie.be') // only an email address is required
    ->attendee('brent@spatie.be', 'Brent')
    ...
```

You can also set the participation status of an attendee:

``` php
Event::create()
    ->attendee('ruben@spatie.be', 'Ruben', ParticipationStatus::Accepted)
    ...
```

There are five participation statuses:

- `ParticipationStatus::Accepted`
- `ParticipationStatus::Declined`
- `ParticipationStatus::Tentative`
- `ParticipationStatus::NeedsAction`
- `ParticipationStatus::Delegated`


You can indicate that an attendee is required to RSVP to an event:

``` php
Event::create()
    ->attendee('ruben@spatie.be', 'Ruben', ParticipationStatus::NeedsAction, requiresResponse: true)
    ...
```

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
    ->status(EventStatus::Cancelled)
    ...
```

There are three event statuses:

- `EventStatus::Confirmed`
- `EventStatus::Cancelled`
- `EventStatus::Tentative`

An event can be classified(`Public`, `Private`, `Confidential`) as such:

``` php
Event::create()
    ->classification(Classification::Private)
    ...
```

You can add a url attachment as such:

```php
Event::create()
    ->attachment('https://spatie.be/logo.svg')
    ->attachment('https://spatie.be/feed.xml', 'application/json')
    ...
```

You can add an embedded attachment (base64) as such:

```php
Event::create()
    ->embeddedAttachment($file->toString())
    ->embeddedAttachment($fileString, 'application/json')
    ->embeddedAttachment($base64String, 'application/json', needsEncoding: false)
    ...
```

You can add an image as such:

``` php
Event::create()
    ->image('https://spatie.be/logo.svg')
    ->image('https://spatie.be/logo.svg', 'text/svg+xml')
    ->image('https://spatie.be/logo.svg', 'text/svg+xml', Display::Badge)
    ...
```

There are four different image display types:

- `Display::Badge`
- `Display::Graphic`
- `Display::Fullsize`
- `Display::Thumbnail`

You can add a sequence to an event as such:

``` php
Event::create()
    ->sequence(1)
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

You can use the popular [Carbon library](https://carbon.nesbot.com/):

``` php
use Carbon\Carbon;

Event::create('Laracon Online')
    ->startsAt(Carbon::now())
    ...
```

#### Timezones

Events will use the [timezones]((https://www.php.net/manual/en/datetime.settimezone.php)) defined in the `DateTime` objects you provide. PHP always sets these timezones in a `DateTime` object. By default, this will be the UTC timezone, but it is possible to [change](https://www.php.net/manual/en/function.date-default-timezone-set.php) this.

Just a reminder: do not use PHP's `setTimezone` function on a `DateTime` object, it will change the time according to the timezone! It is better to create a new `DateTime` object with a timezone as such:

``` php
new DateTime('6 march 2019 15:00', new DateTimeZone('Europe/Brussels'))
```

A point can be made for omitting timezones. For example, when you want to show an event at noon in the world. We define noon at 12 o'clock, but that time is relative. It is not the same for people in Belgium, Australia, or any other country in the world.

That's why you can disable timezones on events:

``` php
$starts = new DateTime('6 march 2019 12:00')

Event::create()
    ->startsAt($starts)
    ->withoutTimezone()
    ...
```

You can even disable timezones for a whole calendar:

``` php
Calendar::create()
   ->withoutTimezone()
    ...
```

Each calendar should have Timezone components describing the timezones used within your calendar. Although not all calendar clients require this, it is recommended to add these components.

Creating such Timezone components is quite complicated. That's why this package will automatically add them for you without configuration.

You can disable this behaviour as such:

``` php
Calendar::create()
   ->withoutAutoTimezoneComponents()
    ...
```

Quick note, when using UTC offsets as timezones(e.g. `+02:00`), no Timezone components will be added automatically and the dates will automatically be converted to UTC.

``` php
$starts = new DateTime('6 march 2019 12:00', new DateTimeZone('+02:00'))

Event::create()->startsAt($starts); // DTSTART will be 20190306T100000Z
```

You can manually add timezones to a calendar if desired as such:

```php
$timezoneEntry = TimezoneEntry::create(
    TimezoneEntryType::Daylight,
    new DateTime('23 march 2020'),
    '+00:00',
    '+02:00'
);

$timezone = Timezone::create('Europe/Brussels')
    ->entry($timezoneEntry)
    ...

Calendar::create()
    ->timezone($timezone)
    ...
```

More on these timezones later.

#### Alerts

Alerts allow calendar clients to send reminders about specific events. For example, Apple Mail on an iPhone will send users a notification about the event. An alert always belongs to an event has a description and a number of minutes before the event it will be triggered:

``` php
Event::create('Laracon Online')
    ->alertMinutesBefore(5, 'Laracon online is going to start in five minutes');
```

You can also trigger an alert after the event:

``` php
Event::create('Laracon Online')
    ->alertMinutesAfter(5, 'Laracon online has ended, see you next year!');
```

Or trigger an alert on a specific date:

``` php
Event::create('Laracon Online')
    ->alertAt(
       new DateTime('05/16/2020 12:00:00'),
       'Laracon online has ended, see you next year!'
    );
```

Removing timezones on a calendar or event will also remove timezones on the alert.


### Repeating events

It is possible for events to repeat, for example your monthly company dinner. This can be done as such:

```php
Event::create('Laracon Online')
    ->repeatOn(new DateTime('05/16/2020 12:00:00'));
```

And you can also repeat the event on a set of dates:

```php
Event::create('Laracon Online')
    ->repeatOn([new DateTime('05/16/2020 12:00:00'), new DateTime('08/13/2020 15:00:00')]);
```

#### Recurrence rules

Recurrence rules or RRule's in short, make it possible to add a repeating event in your calendar by describing when it repeats within an RRule. First, we have to create an RRule:

```php
$rrule = RRule::frequency(RecurrenceFrequency::Daily);
```

This rule describes an event that will be repeated daily. You can also set the frequency to `secondly`, `minutely`, `hourly`, `weekly`, `monthly` or `yearly`.

The RRULE can be added to an event as such:

``` php
Event::create('Laracon Online')
    ->rrule(RRule::frequency(RecurrenceFrequency::Monthly));
```

It is possible to finetune the RRule to your personal taste; let's have a look!

A RRule can start from a certain point in time:

```php
$rrule = RRule::frequency(RecurrenceFrequency::Daily)->starting(new DateTime('now'));
```

And stop at a certain point:

```php
$rrule = RRule::frequency(RecurrenceFrequency::Daily)->until(new DateTime('now'));
```

It can only be repeated for a few times, 10 times for example:

```php
$rrule = RRule::frequency(RecurrenceFrequency::Daily)->times(10);
```

The interval of the repetition can be changed:

```php
$rrule = RRule::frequency(RecurrenceFrequency::Daily)->interval(2);
```

When this event starts on Monday, for example, the next repetition of this event will not occur on Tuesday but Wednesday. You can do the same for all the frequencies.

It is also possible to repeat the event on a specific weekday:

```php
$rrule = RRule::frequency(RecurrenceFrequency::Monthly)->onWeekDay(
   RecurrenceDay::Friday
);
```

Or on a specific weekday of a week in the month:

```php
$rrule = RRule::frequency(RecurrenceFrequency::Monthly)->onWeekDay(
   RecurrenceDay::Friday, 3
);
```

Or on the last weekday of a month:

```php
$rrule = RRule::frequency(RecurrenceFrequency::Monthly)->onWeekDay(
   RecurrenceDay::Sunday, -1
);
```

You can repeat on a specific day in the month:

```php
$rrule = RRule::frequency(RecurrenceFrequency::Monthly)->onMonthDay(16);
```

It is even possible to give an array of days in the month:

```php
$rrule = RRule::frequency(RecurrenceFrequency::Monthly)->onMonthDay(
   [5, 10, 15, 20]
);
```

Repeating can be done for certain months (for example only in the second quarter):

```php
$rrule = RRule::frequency(RecurrenceFrequency::Monthly)->onMonth(
   [RecurrenceMonth::April, RecurrenceMonth::May, RecurrenceMonth::June]
);
```

Or just on one month only:

```php
$rrule = RRule::frequency(RecurrenceFrequency::Monthly)->onMonth(
   RecurrenceMonth::October
);
```

It is possible to set the day when the week starts:

```php
$rrule = RRule::frequency(RecurrenceFrequency::Monthly)->weekStartsOn(
   ReccurenceDay::monday()
);
```

You can provide a specific date on which an event won't be repeated:

```php
Event::create('Laracon Online')
    ->rrule(RRule::frequency(RecurrenceFrequency::Daily))
    ->doNotRepeatOn(new DateTime('05/16/2020 12:00:00'));
```

It is also possible to give an array of dates on which the event won't be repeated:

```php
Event::create('Laracon Online')
    ->rrule(RRule::frequency(RecurrenceFrequency::Daily))
    ->doNotRepeatOn([new DateTime('05/16/2020 12:00:00'), new DateTime('08/13/2020 15:00:00')]);
```

Alternatively you can add RRules as a string:

```php
Event::create('SymfonyCon')
    ->rruleAsString('FREQ=DAILY;INTERVAL=1');
```

If you add RRules as a string the timezones included in DTSTART and UNTIL are unknown to the package as the string is never parsed and evaluated. If they are known you can add DTSTART and UNTIL separately to help the package discover the timezones:

```php
Event::create('SymfonyCon')
    ->rruleAsString(
        'DTSTART=20231207T090000Z;FREQ=DAILY;INTERVAL=1;UNTIL=20231208T090000Z',
        new DateTime('7 december 2023 09:00:00', new DateTimeZone('UTC')),
        new DateTime('8 december 2023 09:00:00', new DateTimeZone('UTC'))
    );
```

### Todo's

It is possible to add todo's to a calendar as such:

```php
$todo = Todo::create('My first todo')

$calendar = Calendar::create('My calendar')->todo($todo);
```

Adding an array of todo's or using a closure for creating an inline todo is also possible, similar to events.

A todo can be at a specified date:

```php
Todo::create()
    ->starts(new DateTime('2023-12-31 23:59:59'))
    ...
```

And have a duration:

```php
Todo::create()
    ->duration(new DateInterval('PT1H30M'))
    ...
```

Please notice that a todo with a duration always needs a start date.

It is also possible to set the due date of a todo:

```php
Todo::create()
    ->due(new DateTime('2023-12-31 23:59:59'))
    ...
```

It is impossible to set a start date and a due date on a todo, only one of them can be set.

A todo can have a completion date:

```php
Todo::create()
    ->completedAt(new DateTime('2023-12-25 12:00:00'))
    ...
```

You can set the percentage complete as such:

```php
Todo::create()
    ->percentComplete(75) // In a range from 0 to 100
    ...
```  

It is possible to set the priority of a todo:

```php
Todo::create()
    ->priority(1) // In a range from 0 to 9
    ...
```

The status of a todo can be set:

```php
Todo::create()
    ->status(TodoStatus::Completed)
    ...
```

There are four todo statuses:

- `TodoStatus::NeedsAction`
- `TodoStatus::Completed`
- `TodoStatus::InProcess`
- `TodoStatus::Cancelled`

A todo has many similarities with events so the following methods are also available on todos:

```php
Todo::create()
  ->description('Meeting about project updates')
  ->uniqueIdentifier('event-12345')
  ->createdAt(new DateTime('2024-01-15 09:00:00'))
  ->withoutTimezone()
  ->classification(Classification::Public)
  ->url('https://example.com/details')
  ->sequence(1)
  ->attendee('john@example.com')
  ->attachment('https://example.com/file.pdf')
  ->address('123 Main St, New York')
  ->addressName('Conference Room A')
  ->coordinates(40.7128, -74.0060)
  ->organizer('admin@example.com')
  ->rrule('FREQ=DAILY')
  ->alert(Alert::minutesBeforeStart(15))
```

### Use with Laravel

You can use Laravel Responses to stream to calendar applications:

``` php
$calendar = Calendar::create('Laracon Online');

return response($calendar->get())
    ->header('Content-Type', 'text/calendar; charset=utf-8');
```

If you want to add the possibility for users to download a calendar and import it into a calendar application:

``` php
$calendar = Calendar::create('Laracon Online');

return response($calendar->get(), 200, [
   'Content-Type' => 'text/calendar; charset=utf-8',
   'Content-Disposition' => 'attachment; filename="my-awesome-calendar.ics"',
]);
```

### Crafting Timezones

If you want to craft timezone components yourself, you're in the right place, although we advise you to read the [section](https://tools.ietf.org/html/rfc5545#section-3.6.5) on timezones from the RFC first.

You can create a timezone as such:

```php
$timezone = Timezone::create('Europe/Brussels');
```

It is possible to provide the last modified date:

```php
$timezone = Timezone::create('Europe/Brussels')
    ->lastModified(new DateTime('16 may 2020 12:00:00'));
```

Or add an url with more information about the timezone:

```php
$timezone = Timezone::create('Europe/Brussels')
    ->url('https://spatie.be');
```

A timezone consists of multiple entries where the time of the timezone changed relative to UTC, such entry can be constructed for standard or daylight time:

```php
$entry = TimezoneEntry::create(
    TimezoneEntryType::Standard,
    new DateTime('16 may 2020 12:00:00'),
    '+00:00',
    '+02:00'
);
```

Firstly you provide the type of entry (`standard` or `daylight`). Then a `DateTime` when the time changes. Lastly, an offset relative to UTC from before the change and an offset relative to UTC after the change.

It is also possible to give this entry a name and description:

```php
$entry = TimezoneEntry::create(...)
    ->name('Europe - Brussels')
    ->description('Belgian timezones ftw!');
```

An RRule for the entry can be given as such:

```php
$entry = TimezoneEntry::create(...)
    ->rrule(RRule::frequency(RecurrenceFrequency::Daily));
```

In the end you can add an entry to a timezone:

```php
$timezone = Timezone::create('Europe/Brussels')
   ->entry($timezoneEntry);
```

Or even add multiple entries:

```php
$timezone = Timezone::create('Europe/Brussels')
   ->entry([$timezoneEntryOne, $timezoneEntryTwo]);
```

Now we've constructed our timezone it is time(👀) to add this timezone to our calendar:

```php
$calendar = Calendar::create('Calendar with timezones')
   ->timezone($timezone);
```

It is also possible to add multiple timezones:

```php
$calendar = Calendar::create('Calendar with timezones')
   ->timezone([$timezoneOne, $timezoneTwo]);
```


### Extending the package

We try to keep this package as straightforward as possible. That's why a lot of properties and subcomponents from the RFC are not included in this package. We've made it possible to add other properties or subcomponents to each component if you might need something not included in the package. But be careful! From this moment, you're on your own correctly implementing the RFC's.

#### Appending properties

You can add a new property to a component like this:

```php
Calendar::create()
    ->appendProperty(
        TextProperty::create('ORGANIZER', 'ruben@spatie.be')
    )
    ...
```

Here we've added a `TextProperty `, and this is a default key-value property type with a text as value. You can also use one of the default properties included in the package or create your own by extending the `Property` class.

Sometimes a property can have some additional parameters, these are key-value entries and can be added to properties as such:

```php
$property = TextProperty::create('ORGANIZER', 'ruben@spatie.be')
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

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

### Security

If you've found a bug regarding security please mail [security@spatie.be](mailto:security@spatie.be) instead of using the issue tracker.

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Kruikstraat 22, box 12, 2018 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Credits

- [Ruben Van Assche](https://github.com/rubenvanassche)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
