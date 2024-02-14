---
name: Bug report
about: Create a report to help us improve icalendar-generator
title: ''
labels: ''
assignees: ''
---

**✏️ Describe the bug**
A clear and concise description of what the bug is.

**↪️ To Reproduce**
Provide us a test like this one which shows the problem:

```php

it('cannot create a calendar', function () {
    $event = Event::create('Event')
        ->startsAt(new DateTime('2021-01-01 12:00:00'))
        ->endsAt(new DateTime('2021-01-01 13:00:00'))
        ->description('Description')
        ->address('Address')
        ->url('https://www.spatie.be')
        ->create();

    // Calendar is not created (off course, since we've only created an event but for documentation purposes)
    dd($event->get());
});
```

Assertions aren't required, a simple dump or dd statement of what's going wrong is good enough 😄

**✅ Expected behavior**
A clear and concise description of what you expected to happen.

**🖥️ Versions**

iCalendar generator:
PHP:
