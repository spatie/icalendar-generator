<?php

namespace Spatie\IcalendarGenerator\Properties;

use Spatie\IcalendarGenerator\ValueObjects\BinaryValue;

final class BinaryProperty extends Property
{
    public static function create(string $name, BinaryValue $binaryValue): BinaryProperty
    {
        return new self($name, $binaryValue);
    }

    public function __construct(string $name, protected BinaryValue $binaryValue)
    {
        $this->name = $name;

        if ($this->binaryValue->fmttype) {
            $this->addParameter(Parameter::create('FMTTYPE', $this->binaryValue->fmttype));
        }

        $this->addParameter(Parameter::create('ENCODING', 'BASE64'));
        $this->addParameter(Parameter::create('VALUE', 'BINARY'));
    }

    public function getValue(): string
    {
        return $this->binaryValue->data;
    }

    public function getOriginalValue(): BinaryValue
    {
        return $this->binaryValue;
    }
}
