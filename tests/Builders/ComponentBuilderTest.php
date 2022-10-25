<?php

namespace Spatie\IcalendarGenerator\Tests\Builders;

use Spatie\IcalendarGenerator\Builders\ComponentBuilder;
use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Tests\TestClasses\DummyComponent;
use Spatie\IcalendarGenerator\Tests\TestClasses\DummyProperty;

use function PHPUnit\Framework\assertEquals;

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

    assertEquals(
        <<<EOT
BEGIN:VTEST\r
location:This is a really long text. Possibly you will never write a text l\r
 ike this in a property. But hey we support the RFC so let us chip it! You \r
 can maybe write some HTML in here that will make it longer than usual.\r
END:VTEST
EOT,
        $builder->build()
    );
});
