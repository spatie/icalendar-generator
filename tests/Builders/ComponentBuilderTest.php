<?php

use function PHPUnit\Framework\assertEquals;
use Spatie\IcalendarGenerator\Builders\ComponentBuilder;
use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Tests\TestClasses\DummyComponent;

use Spatie\IcalendarGenerator\Tests\TestClasses\DummyProperty;

test('it can build a component payload with properties', function () {
    $payload = ComponentPayload::create('VTEST');

    $payload->property(new DummyProperty('location', 'Antwerp'));

    $builder = new ComponentBuilder($payload);

    assertEquals(
        <<<EOT
            BEGIN:VTEST\r
            location:Antwerp\r
            END:VTEST
            EOT,
        $builder->build()
    );
});

test('it can build a component payload with property alias', function () {
    $payload = ComponentPayload::create('VTEST');

    $payload->property(
        (new DummyProperty('location', 'Antwerp'))->addAlias('geo')
    );

    $builder = new ComponentBuilder($payload);

    assertEquals(
        <<<EOT
            BEGIN:VTEST\r
            location:Antwerp\r
            geo:Antwerp\r
            END:VTEST
            EOT,
        $builder->build()
    );
});

test('it can build a component payload with sub-components', function () {
    $payload = ComponentPayload::create('VTEST');

    $payload->subComponent(new DummyComponent('SUBCOMPONENT'));

    $builder = new ComponentBuilder($payload);

    assertEquals(
        <<<EOT
            BEGIN:VTEST\r
            BEGIN:VDUMMY\r
            name:SUBCOMPONENT\r
            END:VDUMMY\r
            END:VTEST
            EOT,
        $builder->build()
    );
});

test('it will chip a line when it becomes too long', function () {
    $payload = ComponentPayload::create('VTEST');

    $payload->property(new DummyProperty('location', 'This is a really long text. Possibly you will never write a text like this in a property. But hey we support the RFC so let us chip it! You can maybe write some HTML in here that will make it longer than usual.'));

    $builder = new ComponentBuilder($payload);

    // The folded line ends on a space, written as a variable so it does not get trimmed away
    $trailingSpace = ' ';

    assertEquals(
        <<<EOT
            BEGIN:VTEST\r
            location:This is a really long text. Possibly you will never write a text l\r
             ike this in a property. But hey we support the RFC so let us chip it! You{$trailingSpace}\r
             can maybe write some HTML in here that will make it longer than usual.\r
            END:VTEST
            EOT,
        $builder->build()
    );
});

test('it keeps the order of lines with multiple properties and sub-components', function () {
    $payload = ComponentPayload::create('VTEST');

    $payload->property(new DummyProperty('location', 'Antwerp'));
    $payload->property(
        (new DummyProperty('url', 'https://spatie.be'))->addAlias('x-url', 'x-website')
    );

    $secondComponent = new DummyComponent('SECOND');
    $secondComponent->description = 'A description that is long enough to be chipped over multiple lines in the output';

    $payload->subComponent(new DummyComponent('FIRST'), $secondComponent);

    $builder = new ComponentBuilder($payload);

    assertEquals(
        <<<EOT
            BEGIN:VTEST\r
            location:Antwerp\r
            url:https://spatie.be\r
            x-url:https://spatie.be\r
            x-website:https://spatie.be\r
            BEGIN:VDUMMY\r
            name:FIRST\r
            END:VDUMMY\r
            BEGIN:VDUMMY\r
            name:SECOND\r
            description:A description that is long enough to be chipped over multiple l\r
             ines in the output\r
            END:VDUMMY\r
            END:VTEST
            EOT,
        $builder->build()
    );
});

test('it preserves named alias overwrites across properties and nested components', function () {
    $payload = ComponentPayload::create('VTEST');

    $payload->property(
        (new DummyProperty('location', 'Antwerp'))->addAlias('x-positional', shared: 'x-first', unique: 'x-unique')
    );
    $payload->property(
        (new DummyProperty('url', 'https://spatie.be'))->addAlias(shared: 'x-second', local: 'x-local-first')
    );
    $payload->property(
        (new DummyProperty('description', 'Brussels'))->addAlias(local: 'x-local-last')
    );

    $firstComponent = new DummyComponent('FIRST');
    $firstComponent->appendProperty(
        (new DummyProperty('alpha', 'One'))->addAlias(shared: 'x-child-first', sibling: 'x-sibling-first')
    );
    $secondComponent = new DummyComponent('SECOND');
    $secondComponent->appendProperty(
        (new DummyProperty('beta', 'Two'))->addAlias(sibling: 'x-sibling-last')
    );
    $secondComponent->appendSubComponent(
        (new DummyComponent('NESTED'))->appendProperty(
            (new DummyProperty('gamma', 'Three'))->addAlias(shared: 'x-nested-last')
        )
    );

    $payload->subComponent($firstComponent, $secondComponent);

    expect((new ComponentBuilder($payload))->build())->toBe(implode("\r\n", [
        'BEGIN:VTEST',
        'location:Antwerp',
        'x-positional:Antwerp',
        'x-nested-last:Three',
        'x-unique:Antwerp',
        'url:https://spatie.be',
        'x-local-last:Brussels',
        'description:Brussels',
        'BEGIN:VDUMMY',
        'name:FIRST',
        'alpha:One',
        'x-sibling-last:Two',
        'END:VDUMMY',
        'BEGIN:VDUMMY',
        'name:SECOND',
        'beta:Two',
        'BEGIN:VDUMMY',
        'name:NESTED',
        'gamma:Three',
        'END:VDUMMY',
        'END:VDUMMY',
        'END:VTEST',
    ]));
});
