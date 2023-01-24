<?php

namespace Spatie\IcalendarGenerator\ValueObjects;

class BinaryValue
{
    public string $data;

    public ?string $fmttype;

    public function __construct(
        string $data,
        ?string $fmttype = null,
        bool $needsEncoding = true
    ) {
        $this->data = $needsEncoding
            ? base64_encode($data)
            : $data;

        $this->fmttype = $fmttype;
    }
}
