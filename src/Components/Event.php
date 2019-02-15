<?php

namespace Spatie\Calendar\Components;

use DateTime;
use Spatie\Calendar\ComponentPayload;

class Event implements Component
{
    /** @var DateTime */
    protected $starts;

    /** @var DateTime */
    protected $ends;

    /** @var string */
    protected $description;

    /** @var string */
    protected $name;

    /** @var string */
    protected $uuid;

    /** @var DateTime */
    protected $created;

    public static function create(): Event
    {
        return new self();
    }

    public function __construct()
    {
        $this->uuid = uniqid();
        $this->created = new DateTime();
    }

    public function starts(DateTime $starts): Event
    {
        $this->starts = $starts;

        return $this;
    }

    public function ends(DateTime $ends): Event
    {
        $this->ends = $ends;

        return $this;
    }

    public function period(DateTime $starts, DateTime $ends): Event
    {
        $this->starts = $starts;
        $this->ends = $ends;

        return $this;
    }

    public function description(string $description): Event
    {
        $this->description = $description;

        return $this;
    }

    public function name(string $name): Event
    {
        $this->name = $name;

        return $this;
    }

    public function getPayload(): ComponentPayload
    {
        return ComponentPayload::new('EVENT')
            ->add('UID', $this->uuid)
            ->add('SUMMARY', $this->name)
            ->add('DESCRIPTION', $this->description)
            ->addDateTime('DTSTART', $this->starts)
            ->addDateTime('DTEND', $this->ends)
            ->addDateTime('DTSTAMP', $this->created);
    }
}
