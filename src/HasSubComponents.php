<?php

namespace Spatie\Calendar;

use Closure;
use Exception;
use ReflectionFunction;
use Spatie\Calendar\Components\Component;

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
            $reflection = new ReflectionFunction($subComponent);

            $this->ensureAComponentIsInjected($reflection);

            $newComponent = $this->buildFreshSubComponent($reflection);

            $subComponent = $reflection->invoke($newComponent) ?? $newComponent;
        }

        $this->subComponents[] = $subComponent;

        return $this;
    }

    protected function ensureAComponentIsInjected(ReflectionFunction $reflection) : bool
    {
        if (count($reflection->getParameters()) !== 1) {
            throw new Exception('Exactly one parameter should be used with closure');
        }

        if (! $reflection->getParameters()[0]->getClass()->isSubclassOf(Component::class)) {
            throw new Exception('A component should be given to the closures parameter');
        }

        return true;
    }

    protected function buildFreshSubComponent(ReflectionFunction $reflection): Component
    {
        return $reflection->getParameters()[0]->getClass()->newInstance();
    }
}
