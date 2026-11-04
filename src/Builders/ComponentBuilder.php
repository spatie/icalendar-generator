<?php

namespace Spatie\IcalendarGenerator\Builders;

use Spatie\IcalendarGenerator\ComponentPayload;

class ComponentBuilder
{
    public static function create(ComponentPayload $componentPayload): self
    {
        return new self($componentPayload);
    }

    public function __construct(protected ComponentPayload $componentPayload)
    {
    }

    public function build(): string
    {
        $lines = [];

        foreach ($this->buildComponent() as $line) {
            foreach ($this->chipLine($line) as $chippedLine) {
                $lines[] = $chippedLine;
            }
        }

        return implode("\r\n", $lines);
    }

    /**  @return array<string> */
    public function buildComponent(): array
    {
        $lines = ["BEGIN:{$this->componentPayload->getType()}"];

        $lines = array_merge(
            $lines,
            $this->buildProperties(),
            $this->buildSubComponents()
        );

        $lines[] = "END:{$this->componentPayload->getType()}";

        return $lines;
    }

    /**  @return array<string> */
    protected function buildProperties(): array
    {
        $lines = [];

        foreach ($this->componentPayload->getProperties() as $property) {
            $builder = new PropertyBuilder($property);

            foreach ($builder->build() as $line) {
                $lines[] = $line;
            }
        }

        return $lines;
    }

    /**  @return array<string> */
    protected function buildSubComponents(): array
    {
        $lines = [];

        foreach ($this->componentPayload->getSubComponents() as $component) {
            $builder = new ComponentBuilder($component->resolvePayload());

            foreach ($builder->buildComponent() as $line) {
                $lines[] = $line;
            }
        }

        return $lines;
    }

    /**  @return array<string> */
    protected function chipLine(string $line): array
    {
        $chippedLines = [];

        while (strlen($line) > 0) {
            if (strlen($line) > 75) {
                $chippedLines[] = mb_strcut($line, 0, 75, 'utf-8');
                $line = ' '.mb_strcut($line, 75, strlen($line), 'utf-8');
            } else {
                $chippedLines[] = $line;

                break;
            }
        }

        return $chippedLines;
    }
}
