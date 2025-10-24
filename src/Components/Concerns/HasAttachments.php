<?php

namespace Spatie\IcalendarGenerator\Components\Concerns;

use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Properties\BinaryProperty;
use Spatie\IcalendarGenerator\Properties\Parameter;
use Spatie\IcalendarGenerator\Properties\UriProperty;
use Spatie\IcalendarGenerator\ValueObjects\BinaryValue;

trait HasAttachments
{
    /** @var array<array{url: string, type: string|null}|BinaryValue> */
    protected array $attachments = [];

    public function attachment(string $url, ?string $mediaType = null): self
    {
        $this->attachments[] = [
            'url' => $url,
            'type' => $mediaType,
        ];

        return $this;
    }

    public function embeddedAttachment(
        string $data,
        ?string $mediaType = null,
        bool $needsEncoding = true
    ): self {
        $this->attachments[] = new BinaryValue($data, $mediaType, $needsEncoding);

        return $this;
    }

    protected function resolveAttachmentProperties(ComponentPayload $payload): self
    {
        foreach ($this->attachments as $attachment) {
            $property = match (true) {
                $attachment instanceof BinaryValue => BinaryProperty::create('ATTACH', $attachment),
                $attachment['type'] !== null => UriProperty::create('ATTACH', $attachment['url'])->addParameter(Parameter::create('FMTTYPE', $attachment['type'])),
                default => UriProperty::create('ATTACH', $attachment['url']),
            };

            $payload->property($property);
        }

        return $this;
    }
}
