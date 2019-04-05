<?php

namespace Spatie\Calendar;

use Closure;
use Exception;
use ReflectionFunction;
use Spatie\Calendar\Components\Component;

trait HasSubComponents
{
    /** @var array */
    private $subComponents = [];

    /**
     * @param $subComponent \Spatie\Calendar\Components\Component|array|Closure
     *
     * @return \Spatie\Calendar\Components\Component
     */
    private function addSubComponent($subComponent): Component
    {
        if(is_null($subComponent)){
            return $this;
        }

        $subcomponents = is_array($subComponent) ? $subComponent : [$subComponent];

        $this->subComponents = array_map(function($component){
            return $this->resolveSubComponent($component);
        }, $subcomponents);

        return $this;
    }

    /**
     * @param $subComponent Component|Closure
     *
     * @return Component
     * @throws \ReflectionException
     */
    private function resolveSubComponent($subComponent): Component
    {
        if ($subComponent instanceof Closure) {
            $reflection = new ReflectionFunction($subComponent);

            $this->ensureAComponentIsInjected($reflection);

            $newComponent = $this->buildFreshSubComponent($reflection);

            $subComponent = $reflection->invoke($newComponent) ?? $newComponent;
        }

        return $subComponent;
    }

    private function ensureAComponentIsInjected(ReflectionFunction $reflection): bool
    {
        if (count($reflection->getParameters()) !== 1) {
            throw new Exception('Exactly one parameter should be used with closure');
        }

        if (! $reflection->getParameters()[0]->getClass()->isSubclassOf(Component::class)) {
            throw new Exception('A component should be given to the closures parameter');
        }

        return true;
    }

    private function buildFreshSubComponent(ReflectionFunction $reflection): Component
    {
        return $reflection->getParameters()[0]->getClass()->newInstance();
    }
}
