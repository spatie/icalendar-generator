<?php

namespace Spatie\Calendar\Builders;

use Spatie\Calendar\ComponentPayload;

class ComponentBuilder
{
    /** @var \Spatie\Calendar\ComponentPayload */
    protected $componentPayload;

    public function __construct(ComponentPayload $componentPayload)
    {
        $this->componentPayload = $componentPayload;
    }

    public function build(): string
    {
        return implode('\r\n', $this->buildComponent());
    }

    public function buildComponent(): array
    {
        $lines[] = "BEGIN:V{$this->componentPayload->getType()}";

        $lines = array_merge(
            $lines,
            $this->buildProperties(),
            $this->buildSubComponents()
        );

        $lines[] = "END:V{$this->componentPayload->getType()}";

        return $lines;
    }

    protected function buildProperties(): array
    {
        $lines = [];

        foreach ($this->componentPayload->getProperties() as $key => $property) {
            $line = (new PropertyBuilder($property))->build();

            $lines = array_merge(
                $lines,
                $this->chipLine($line)
            );
        }

        return $lines;
    }

    protected function buildSubComponents(): array
    {
        $lines = [];

        /** @var \Spatie\Calendar\Components\Component $component */
        foreach ($this->componentPayload->getSubComponents() as $component) {
            $builder = new ComponentBuilder($component->getPayload());

            $lines = array_merge(
                $lines,
                $builder->buildComponent()
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
