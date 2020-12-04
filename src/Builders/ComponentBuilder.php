<?php

namespace Spatie\IcalendarGenerator\Builders;

use Spatie\IcalendarGenerator\ComponentPayload;

class ComponentBuilder
{
    private ComponentPayload $componentPayload;

    public static function create(ComponentPayload $componentPayload): self
    {
        return new self($componentPayload);
    }

    public function __construct(ComponentPayload $componentPayload)
    {
        $this->componentPayload = $componentPayload;
    }

    public function build(): string
    {
        $lines = [];

        foreach ($this->buildComponent() as $line) {
            $lines = array_merge($lines, $this->chipLine($line));
        }

        return implode("\r\n", $lines);
    }

    public function buildComponent(): array
    {
        $lines[] = "BEGIN:{$this->componentPayload->getType()}";

        $lines = array_merge(
            $lines,
            $this->buildProperties(),
            $this->buildSubComponents()
        );

        $lines[] = "END:{$this->componentPayload->getType()}";

        return $lines;
    }

    private function buildProperties(): array
    {
        $lines = [];

        foreach ($this->componentPayload->getProperties() as $property) {
            $builder = new PropertyBuilder($property);

            $lines = array_merge(
                $lines,
                $builder->build()
            );
        }

        return $lines;
    }

    private function buildSubComponents(): array
    {
        $lines = [];

        /** @var \Spatie\IcalendarGenerator\Components\Component $component */
        foreach ($this->componentPayload->getSubComponents() as $component) {
            $builder = new ComponentBuilder($component->resolvePayload());

            $lines = array_merge(
                $lines,
                $builder->buildComponent()
            );
        }

        return $lines;
    }

    private function chipLine(string $line): array
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
