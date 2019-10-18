<?php

namespace Spatie\IcalendarGenerator\PropertyTypes;

final class TextPropertyType extends PropertyType
{
    /** @var string */
    private $text;

    /**
     * TextPropertyType constructor.
     *
     * @param array|string $names
     * @param string $text
     */
    public function __construct($names, string $text)
    {
        parent::__construct($names);

        $this->text = $text;
    }

    public function getValue(): string
    {
        $replacements = [
            '\\' => '\\\\',
            '"' => '\\"',
            ',' => '\\,',
            ';' => '\\;',
            "\n" => '\\n',
        ];

        return str_replace(array_keys($replacements), $replacements, $this->text);
    }

    public function getOriginalValue() : string
    {
        return $this->text;
    }
}
