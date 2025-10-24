<?php

namespace Spatie\IcalendarGenerator\Components\Concerns;

use DateTimeInterface;
use Spatie\IcalendarGenerator\ComponentPayload;
use Spatie\IcalendarGenerator\Components\Alert;

trait HasAlerts
{

    /** @var Alert[] */
    protected array $alerts = [];

    public function alert(Alert $alert): self
    {
        $this->alerts[] = $alert;

        return $this;
    }

    public function alertAt(DateTimeInterface $alert, ?string $message = null): self
    {
        $this->alerts[] = Alert::date($alert, $message);

        return $this;
    }

    public function alertMinutesBefore(int $minutes, ?string $message = null): self
    {
        $this->alerts[] = Alert::minutesBeforeStart($minutes, $message);

        return $this;
    }

    public function alertMinutesAfter(int $minutes, ?string $message = null): self
    {
        $this->alerts[] = Alert::minutesAfterEnd($minutes, $message);

        return $this;
    }

    protected function resolveAlerts(ComponentPayload $payload): self
    {
        $alerts = array_map(
            fn (Alert $alert) => $this->withTimezone ? $alert : $alert->withoutTimezone(),
            $this->alerts
        );

        $payload->subComponent(...$alerts);

        return $this;
    }

}
