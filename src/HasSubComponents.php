<?php

namespace Spatie\Calendar;

use Closure;
use ReflectionFunction;
use Spatie\Calendar\Components\Component;

// Todo: this should be tested + we should add some extra checks
trait HasSubComponents
{
    /** @var array */
    protected $subComponents = [];

    protected function addSubComponent($subComponent): Component
    {
        if (is_array($subComponent)) {
            foreach ($subComponent as $item) {
                $this->resolveSubComponent($item);
            }
        } else {
            $this->resolveSubComponent($subComponent);
        }

        return $this;
    }

    protected function resolveSubComponent($subComponent)
    {
        if ($subComponent instanceof Closure) {
            $freshSubComponent = $this->buildFreshSubComponent($subComponent);

            $subComponent($freshSubComponent);

            $subComponent = $freshSubComponent;
        }

        $this->subComponents[] = $subComponent;

        return $this;
    }

    protected function buildFreshSubComponent(Closure $closure) : Component
    {
        $reflection = new ReflectionFunction($closure);

        return $reflection->getParameters()[0]->getClass()->newInstance();
    }
}
