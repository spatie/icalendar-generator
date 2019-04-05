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
        if($subComponent === null){
            return $this;
        }

        $subcomponents = is_array($subComponent) ? $subComponent : [$subComponent];

        array_walk($subcomponents, function($component){
            $this->resolveSubComponent($component);
        });

        return $this;
    }

    /**
     * @param $subComponent Component|Closure
     *
     * @return $this
     * @throws \ReflectionException
     */
    private function resolveSubComponent($subComponent)
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
