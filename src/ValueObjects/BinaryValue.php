<?php

namespace Spatie\IcalendarGenerator\ValueObjects;

class BinaryValue
{
    public string $data;

    public function __construct(
        string $data,
        public ?string $fmttype = null,
        bool $needsEncoding = true
    ) {
        $this->data = $needsEncoding
            ? base64_encode($data)
            : $data;
    }
}
