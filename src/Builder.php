<?php

namespace Spatie\Calendar;

class Builder
{
    /** @var \Spatie\Calendar\ComponentPayload */
    protected $componentPayload;

    public static function new(ComponentPayload $componentPayload): Builder
    {
        return new self($componentPayload);
    }

    public function __construct(ComponentPayload $componentPayload)
    {
        $this->componentPayload = $componentPayload;
    }


    public function build(): string
    {
        return implode('/n', $this->buildComponent());
    }

    public function buildComponent(): array
    {
        $lines[] = "BEGIN:V{$this->componentPayload->getIdentifier()}";

        $lines = array_merge(
            $lines,
            $this->buildProperties(),
            $this->buildSubComponents()
        );

        $lines[] = "END:V{$this->componentPayload->getIdentifier()}";

        return $lines;
    }

    protected function buildProperties(): array
    {
        $lines = [];

        foreach ($this->componentPayload->getProperties() as $key => $property) {
            $lines = array_merge(
                $lines,
                $this->chipLine("{$key}:{$property}")
            );
        }

        return $lines;
    }

    protected function buildSubComponents(): array
    {
        $lines = [];

        /** @var \Spatie\Calendar\Components\Component $component */
        foreach ($this->componentPayload->getComponents() as $component) {
            $lines = array_merge(
                $lines,
                Builder::new($component->getPayload())->buildComponent()
            );
        }

        return $lines;
    }

    protected function chipLine(string $line): array
    {
        $chippedLines = [];

        while (strlen($line) > 0) {
            if (strlen($line) > 75) {
                $chippedLines[] = mb_strcut($line, 0, 75, 'utf-8');
                $line = ' ' . mb_strcut($line, 75, strlen($line), 'utf-8');
            } else {
                $chippedLines[] = $line;
                break;
            }
        }

        return $chippedLines;
    }
}
