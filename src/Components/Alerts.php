<?php

namespace Spatie\IcalendarGenerator\Components;

use DateTimeInterface;
use Spatie\IcalendarGenerator\ComponentPayload;

trait Alerts
{

    /** @var Alert[] */
    protected array $alerts = [];

    public function alert(Alert $alert): static
    {
        $this->alerts[] = $alert;

        return $this;
    }

    public function alertAt(DateTimeInterface $alert, ?string $message = null): self
    {
        $this->alerts[] = Alert::date($alert, $message);

        return $this;
    }

    public function alertMinutesBefore(int $minutes, ?string $message = null): static
    {
        $this->alerts[] = Alert::minutesBeforeStart($minutes, $message);

        return $this;
    }

    public function alertMinutesAfter(int $minutes, ?string $message = null): static
    {
        $this->alerts[] = Alert::minutesAfterEnd($minutes, $message);

        return $this;
    }

    protected function resolveAlerts(ComponentPayload $payload): static
    {
        $alerts = array_map(
            fn (Alert $alert) => $this->withTimezone ? $alert : $alert->withoutTimezone(),
            $this->alerts
        );

        $payload->subComponent(...$alerts);

        return $this;
    }

}
