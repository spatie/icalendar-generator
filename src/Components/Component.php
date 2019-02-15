<?php

namespace Spatie\Calendar\Components;

use Spatie\Calendar\ComponentPayload;

interface Component
{
    public function getPayload() : ComponentPayload;
}
