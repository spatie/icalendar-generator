<?php

use function PHPUnit\Framework\assertStringEndsWith;
use Spatie\IcalendarGenerator\Components\Alert;
use Spatie\IcalendarGenerator\Components\Event;
use Spatie\IcalendarGenerator\Enums\Classification;
use Spatie\IcalendarGenerator\Enums\Display;
use Spatie\IcalendarGenerator\Enums\EventStatus;
use Spatie\IcalendarGenerator\Enums\ParticipationStatus;
use Spatie\IcalendarGenerator\Enums\RecurrenceFrequency;
use Spatie\IcalendarGenerator\Properties\DateTimeProperty;
use Spatie\IcalendarGenerator\Tests\PayloadExpectation;
use Spatie\IcalendarGenerator\Tests\PropertyExpectation;
use Spatie\IcalendarGenerator\ValueObjects\CalendarAddress;

use Spatie\IcalendarGenerator\ValueObjects\RRule;

test('it can create an event', function () {
    $payload = Event::create()->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectType('VEVENT')
        ->expectPropertyCount(2)
        ->expectPropertyExists('UID')
        ->expectPropertyExists('DTSTAMP');
});

test('it can set properties on an event', function () {
    $dateCreated = new DateTime('16 may 2019');
    $dateStarts = new DateTime('17 may 2019');
    $dateEnds = new DateTime('18 may 2019');

    $payload = Event::create('An introduction into event sourcing')
        ->description('By Freek Murze')
        ->createdAt($dateCreated)
        ->url('http://example.com/pub/calendars/jsmith/mytime.ics')
        ->uniqueIdentifier('Identifier here')
        ->startsAt($dateStarts)
        ->endsAt($dateEnds)
        ->address('Antwerp')
        ->addressName('Spatie')
        ->googleConference('Spatie')
        ->googleConference('https://meet.google.com/aaa-aaa-aaa')
        ->microsoftTeams('https://teams.microsoft.com/l/meetup-join/aaa-aaa-aaa')
        ->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectPropertyCount(10)
        ->expectPropertyValue('SUMMARY', 'An introduction into event sourcing')
        ->expectPropertyValue('DESCRIPTION', 'By Freek Murze')
        ->expectPropertyValue('DTSTAMP', $dateCreated)
        ->expectPropertyValue('DTSTART', $dateStarts)
        ->expectPropertyValue('DTEND', $dateEnds)
        ->expectPropertyValue('LOCATION', 'Antwerp')
        ->expectPropertyValue('UID', 'Identifier here')
        ->expectPropertyValue('X-GOOGLE-CONFERENCE', 'https://meet.google.com/aaa-aaa-aaa')
        ->expectPropertyValue('X-MICROSOFT-SKYPETEAMSMEETINGURL', 'https://teams.microsoft.com/l/meetup-join/aaa-aaa-aaa')
        ->expectPropertyValue('URL', 'http://example.com/pub/calendars/jsmith/mytime.ics');
});

test('it can set a period on an event', function () {
    $dateStarts = new DateTime('17 may 2019');
    $dateEnds = new DateTime('18 may 2019');

    $payload = Event::create('An introduction into event sourcing')
        ->period($dateStarts, $dateEnds)
        ->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectPropertyValue('DTSTART', $dateStarts)
        ->expectPropertyValue('DTEND', $dateEnds);
});

test('an event can be a fully day', function () {
    $dateStarts = new DateTime('17 may 2019');
    $dateEnds = new DateTime('18 may 2019');

    $payload = Event::create('An introduction into event sourcing')
        ->fullDay()
        ->period($dateStarts, $dateEnds)
        ->resolvePayload();

    PropertyExpectation::create($payload, 'DTSTART')
        ->expectParameterCount(1)
        ->expectParameterValue('VALUE', 'DATE');

    PropertyExpectation::create($payload, 'DTEND')
        ->expectParameterCount(1)
        ->expectParameterValue('VALUE', 'DATE');
});

test('an event can be a full day with timezones', function () {
    $dateStarts = new DateTime('17 may 2019', new DateTimeZone('Europe/London'));
    $dateEnds = new DateTime('18 may 2019', new DateTimeZone('Europe/London'));

    $payload = Event::create('An introduction into event sourcing')
        ->fullDay()
        ->period($dateStarts, $dateEnds)
        ->resolvePayload();

    PropertyExpectation::create($payload, 'DTSTART')
        ->expectParameterCount(2)
        ->expectParameterValue('VALUE', 'DATE')
        ->expectParameterValue('TZID', 'Europe/London');

    PropertyExpectation::create($payload, 'DTEND')
        ->expectParameterCount(2)
        ->expectParameterValue('VALUE', 'DATE')
        ->expectParameterValue('TZID', 'Europe/London');
});

test('an event can be a full day without specifying an end', function () {
    $dateStarts = new DateTime('17 may 2019');

    $payload = Event::create('An introduction into event sourcing')
        ->fullDay()
        ->startsAt($dateStarts)
        ->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectPropertyMissing('DTEND')
        ->expectProperty('DTSTART', function (PropertyExpectation $expectation) {
            $expectation
                ->expectParameterCount(1)
                ->expectParameterValue('VALUE', 'DATE');
        });
});

test('it can alert minutes before an event', function () {
    $payload = Event::create()
        ->alertMinutesBefore(5)
        ->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectSubComponentCount(1)
        ->expectSubComponentInstanceOf(0, Alert::class);
});

test('it can alert minutes after an event', function () {
    $payload = Event::create()
        ->alertMinutesAfter(5)
        ->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectSubComponentCount(1)
        ->expectSubComponentInstanceOf(0, Alert::class);
});

test('it can add an alert', function () {
    $payload = Event::create()
        ->alert(new Alert('Test'))
        ->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectSubComponentCount(1)
        ->expectSubComponentInstanceOf(0, Alert::class);
});

test('it can set the coordinates', function () {
    $payload = Event::create('An introduction into event sourcing')
        ->coordinates(51.2343, 4.4287)
        ->resolvePayload();

    PropertyExpectation::create($payload, 'GEO')
        ->expectValue(['lat' => 51.2343, 'lng' => 4.4287]);
});

test('it can generate an apple structured location', function () {
    $payload = Event::create('An introduction into event sourcing')
        ->coordinates(51.2343, 4.4287)
        ->address('Samberstraat 69D, 2060 Antwerpen, Belgium')
        ->addressName('Spatie HQ')
        ->resolvePayload();

    PropertyExpectation::create($payload, 'X-APPLE-STRUCTURED-LOCATION')
        ->expectValue(['lat' => 51.2343, 'lng' => 4.4287,])
        ->expectOutput('geo:51.2343,4.4287')
        ->expectParameterValue('VALUE', 'URI')
        ->expectParameterValue('X-ADDRESS', 'Samberstraat 69D\, 2060 Antwerpen\, Belgium')
        ->expectParameterValue('X-APPLE-RADIUS', 72)
        ->expectParameterValue('X-TITLE', 'Spatie HQ');
});

test('it can add a classification', function () {
    $payload = Event::create()
        ->classification(Classification::private())
        ->resolvePayload();

    PropertyExpectation::create($payload, 'CLASS')
        ->expectValue(Classification::private()->value)
        ->expectOutput(Classification::private()->value);
});

test('it can make an event transparent', function () {
    $payload = Event::create()
        ->transparent()
        ->resolvePayload();

    PropertyExpectation::create($payload, 'TRANSP')
        ->expectValue('TRANSPARENT');
});

test('it can add an organizer', function () {
    $payload = Event::create()
        ->organizer('ruben@spatie.be', 'Ruben')
        ->resolvePayload();

    PropertyExpectation::create($payload, 'ORGANIZER')
        ->expectValue(new CalendarAddress('ruben@spatie.be', 'Ruben'));
});

test('it can add attendees', function () {
    $payload = Event::create()
        ->attendee('ruben@spatie.be')
        ->attendee('brent@spatie.be', 'Brent')
        ->attendee('adriaan@spatie.be', 'Adriaan', ParticipationStatus::declined())
        ->attendee('john@spatie.be', 'John', ParticipationStatus::needs_action(), true)
        ->resolvePayload();

    PayloadExpectation::create($payload)->expectPropertyValue(
        'ATTENDEE',
        new CalendarAddress('ruben@spatie.be'),
        new CalendarAddress('brent@spatie.be', 'Brent'),
        new CalendarAddress('adriaan@spatie.be', 'Adriaan', ParticipationStatus::declined()),
        new CalendarAddress('john@spatie.be', 'John', ParticipationStatus::needs_action(), true)
    );
});

test('it can set a status', function () {
    $payload = Event::create()
        ->status(EventStatus::tentative())
        ->resolvePayload();

    PropertyExpectation::create($payload, 'STATUS')
        ->expectValue(EventStatus::tentative()->value);
});

test('it can set an address without name', function () {
    $dateStarts = new DateTime('17 may 2019');
    $dateEnds = new DateTime('18 may 2019');

    $payload = Event::create('An introduction into event sourcing')
        ->startsAt($dateStarts)
        ->endsAt($dateEnds)
        ->address('Antwerp')
        ->resolvePayload();

    PropertyExpectation::create($payload, 'LOCATION')
        ->expectValue('Antwerp');
});

test('it can set a recurrence rule', function () {
    $payload = Event::create('An introduction into event sourcing')
        ->rrule($rrule = RRule::frequency(RecurrenceFrequency::daily()))
        ->resolvePayload();

    PropertyExpectation::create($payload, 'RRULE')
        ->expectValue($rrule);
});

test('it can set a recurrence rule as a string', function () {
    $payload = Event::create('A recurring event')
        ->rruleAsString($rrule = 'FREQ=DAILY;INTERVAL=2;UNTIL=20240301T230000Z')
        ->resolvePayload();

    PropertyExpectation::create($payload, 'RRULE')
        ->expectValue($rrule)
        ->expectOutput($rrule);
});

test('it can create an event without timezones', function () {
    $dateAlert = new DateTime('17 may 2019 11:00:00');
    $dateStarts = new DateTime('17 may 2019 12:00:00');
    $dateEnds = new DateTime('18 may 2019 13:00:00');

    $payload = Event::create('An introduction into event sourcing')
        ->withoutTimezone()
        ->alertAt($dateAlert)
        ->startsAt($dateStarts)
        ->endsAt($dateEnds)
        ->resolvePayload();

    PropertyExpectation::create($payload, 'DTSTART')->expectParameterCount(0);
    PropertyExpectation::create($payload, 'DTEND')->expectParameterCount(0);
    PropertyExpectation::create($payload, 'DTSTAMP')->expectParameterCount(0);

    PayloadExpectation::create($payload)->expectSubComponent(0, function (PayloadExpectation $expectation) {
        $expectation->expectProperty('TRIGGER', function (PropertyExpectation $expectation) {
            $expectation
                ->expectParameterCount(1)
                ->expectParameterValue('VALUE', 'DATE-TIME');
        });
    });
});

test('it can set a url', function () {
    $payload = Event::create()
        ->url('http://example.com/pub/calendars/jsmith/mytime.ics')
        ->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectPropertyValue('URL', 'http://example.com/pub/calendars/jsmith/mytime.ics');
});

test('it ignores a wrong url', function () {
    $payload = Event::create()
        ->url('xample.com/pub/calendars/jsmith/mytime.ics')
        ->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectPropertyMissing('URL');
});

test('it will use UTC for default created date stamp', function () {
    ini_set('date.timezone', 'Europe/Brussels');

    $payload = Event::create()
        ->resolvePayload();

    ini_set('date.timezone', 'UTC');

    assertStringEndsWith('Z', $payload->getProperty('DTSTAMP')->getValue());
});

test('it will always use UTC for a created date stamp ', function () {
    $created = new DateTime('16 may 2020 12:00:00', new DateTimeZone('Europe/Brussels'));

    $payload = Event::create()
        ->createdAt($created)
        ->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectPropertyValue('DTSTAMP', new DateTime('16 may 2020 10:00:00', new DateTimeZone('UTC')));
});

test('it can add recurrence dates', function () {
    PropertyExpectation::create(
        Event::create()->repeatOn(new DateTime('16 may 2020 12:00:00'))->resolvePayload(),
        'RDATE'
    )->expectBuilt('RDATE;VALUE=DATE-TIME:20200516T120000Z');

    PropertyExpectation::create(
        Event::create()->repeatOn(new DateTime('16 may 2020 12:00:00'), false)->resolvePayload(),
        'RDATE'
    )->expectBuilt('RDATE;VALUE=DATE:20200516');
});

test('it can add multiple recurrence dates', function () {
    $dateA = new DateTime('16 may 2019 12:00:00');
    $dateB = new DateTime('16 may 2020 15:00:00');

    $dateC = new DateTime('13 august 2019 12:00:00');
    $dateD = new DateTime('13 august 2020 15:00:00');

    $payload = Event::create()
        ->repeatOn([$dateA, $dateB])
        ->repeatOn([$dateC, $dateD], false)
        ->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectProperty(
            'RDATE',
            function (PropertyExpectation $expectation) use ($dateA) {
                $expectation
                    ->expectInstanceOf(DateTimeProperty::class)
                    ->expectValue($dateA)
                    ->expectParameterCount(1)
                    ->expectParameterValue('VALUE', 'DATE-TIME');
            },
            function (PropertyExpectation $expectation) use ($dateB, $dateA) {
                $expectation
                    ->expectInstanceOf(DateTimeProperty::class)
                    ->expectValue($dateB)
                    ->expectParameterCount(1)
                    ->expectParameterValue('VALUE', 'DATE-TIME');
            },
            function (PropertyExpectation $expectation) use ($dateC, $dateA) {
                $expectation
                    ->expectInstanceOf(DateTimeProperty::class)
                    ->expectValue($dateC)
                    ->expectParameterCount(1)
                    ->expectParameterValue('VALUE', 'DATE');
            },
            function (PropertyExpectation $expectation) use ($dateD, $dateA) {
                $expectation
                    ->expectInstanceOf(DateTimeProperty::class)
                    ->expectValue($dateD)
                    ->expectParameterCount(1)
                    ->expectParameterValue('VALUE', 'DATE');
            }
        );
});

test('it can add excluded recurrence dates', function () {
    PropertyExpectation::create(
        Event::create()->doNotRepeatOn(new DateTime('16 may 2020 12:00:00'))->resolvePayload(),
        'EXDATE'
    )->expectBuilt('EXDATE;VALUE=DATE-TIME:20200516T120000Z');

    PropertyExpectation::create(
        Event::create()->doNotRepeatOn(new DateTime('16 may 2020 12:00:00'), false)->resolvePayload(),
        'EXDATE'
    )->expectBuilt('EXDATE;VALUE=DATE:20200516');
});

test('it can add multiple excluded recurrence dates', function () {
    $dateA = new DateTime('16 may 2019 12:00:00');
    $dateB = new DateTime('16 may 2020 15:00:00');

    $dateC = new DateTime('13 august 2019 12:00:00');
    $dateD = new DateTime('13 august 2020 15:00:00');

    $payload = Event::create()
        ->doNotRepeatOn([$dateA, $dateB])
        ->doNotRepeatOn([$dateC, $dateD], false)
        ->resolvePayload();

    PayloadExpectation::create($payload)
        ->expectProperty(
            'EXDATE',
            function (PropertyExpectation $expectation) use ($dateA) {
                $expectation
                    ->expectInstanceOf(DateTimeProperty::class)
                    ->expectValue($dateA)
                    ->expectParameterCount(1)
                    ->expectParameterValue('VALUE', 'DATE-TIME');
            },
            function (PropertyExpectation $expectation) use ($dateB, $dateA) {
                $expectation
                    ->expectInstanceOf(DateTimeProperty::class)
                    ->expectValue($dateB)
                    ->expectParameterCount(1)
                    ->expectParameterValue('VALUE', 'DATE-TIME');
            },
            function (PropertyExpectation $expectation) use ($dateC, $dateA) {
                $expectation
                    ->expectInstanceOf(DateTimeProperty::class)
                    ->expectValue($dateC)
                    ->expectParameterCount(1)
                    ->expectParameterValue('VALUE', 'DATE');
            },
            function (PropertyExpectation $expectation) use ($dateD, $dateA) {
                $expectation
                    ->expectInstanceOf(DateTimeProperty::class)
                    ->expectValue($dateD)
                    ->expectParameterCount(1)
                    ->expectParameterValue('VALUE', 'DATE');
            }
        );
});

test('it can add an attachment to an event', function () {
    $payload = Event::create()
        ->attachment('http://spatie.be/logo.svg')
        ->attachment('http://spatie.be/logo.jpg', 'application/html')
        ->resolvePayload();

    PayloadExpectation::create($payload)->expectProperty(
        'ATTACH',
        fn (PropertyExpectation $expectation) => $expectation
            ->expectParameterCount(0)
            ->expectValue('http://spatie.be/logo.svg'),
        fn (PropertyExpectation $expectation) => $expectation
            ->expectParameterCount(1)
            ->expectParameterValue('FMTTYPE', 'application/html')
            ->expectValue('http://spatie.be/logo.jpg'),
    );
});

test('it can add an embedded attachment to an event', function () {
    $file = file_get_contents('.gitignore');
    $base64File = base64_encode(file_get_contents('.gitattributes'));

    $payload = Event::create()
        ->embeddedAttachment($file, 'text/plain')
        ->embeddedAttachment($base64File, 'text/plain', false)
        ->resolvePayload();

    PayloadExpectation::create($payload)->expectProperty(
        'ATTACH',
        fn (PropertyExpectation $expectation) => $expectation
            ->expectParameterCount(3)
            ->expectParameterValue('FMTTYPE', 'text/plain')
            ->expectParameterValue('ENCODING', 'BASE64')
            ->expectParameterValue('VALUE', 'BINARY')
            ->expectOutput(base64_encode($file)),
        fn (PropertyExpectation $expectation) => $expectation
            ->expectParameterCount(3)
            ->expectParameterValue('FMTTYPE', 'text/plain')
            ->expectParameterValue('ENCODING', 'BASE64')
            ->expectParameterValue('VALUE', 'BINARY')
            ->expectOutput($base64File),
    );
});

test('it can add an image to an event', function () {
    $payload = Event::create()
        ->image('http://spatie.be/logo.svg')
        ->image('http://spatie.be/logo.jpg', 'image/jpeg')
        ->image('http://spatie.be/logo.png', 'image/png', Display::badge())
        ->resolvePayload();

    PayloadExpectation::create($payload)->expectProperty(
        'IMAGE',
        fn (PropertyExpectation $expectation) => $expectation
            ->expectParameterCount(1)
            ->expectParameterValue('VALUE', 'URI')
            ->expectValue('http://spatie.be/logo.svg'),
        fn (PropertyExpectation $expectation) => $expectation
            ->expectParameterCount(2)
            ->expectParameterValue('VALUE', 'URI')
            ->expectParameterValue('FMTTYPE', 'image/jpeg')
            ->expectValue('http://spatie.be/logo.jpg'),
        fn (PropertyExpectation $expectation) => $expectation
            ->expectParameterCount(3)
            ->expectParameterValue('VALUE', 'URI')
            ->expectParameterValue('FMTTYPE', 'image/png')
            ->expectParameterValue('DISPLAY', 'BADGE')
            ->expectValue('http://spatie.be/logo.png'),
    );
});
