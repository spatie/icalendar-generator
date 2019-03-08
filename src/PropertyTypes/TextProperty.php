<?php

namespace Spatie\Calendar\PropertyTypes;

class TextProperty extends Property
{
    /** @var string */
    protected $text;

    public function __construct(string $name, string $text)
    {
        $this->name = $name;
        $this->text = $text;
    }

    public function getValue(): string
    {
        $replacements = [
            '\\' => '\\\\',
            '"' => '\\"',
            ',' => '\\,',
            ';' => '\\;',
            "\n" => '\\n'
        ];

        return str_replace(array_keys($replacements), $replacements, $this->text);
    }
}
