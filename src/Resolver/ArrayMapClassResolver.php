<?php

namespace Pkerrigan\Di\Resolver;

use Pkerrigan\Di\ClassResolver;
use Pkerrigan\Di\ResolvedClass;
use Pkerrigan\Di\ResolvedClass\Singleton;

/**
 * Lightweight dependency injector
 * 3 Clause BSD Licence
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 13/03/16
 */
class ArrayMapClassResolver implements ClassResolver
{
    /**
     * @var array
     */
    private $mapping;

    /**
     * ArrayMapClassResolver constructor.
     * @param array $mapping
     */
    public function __construct(array $mapping)
    {
        $this->mapping = $this->applyDefaultResolutionType($mapping);
    }

    public function resolveConcreteClass(string $className): ?ResolvedClass
    {
        return $this->mapping[$className] ?? null;
    }

    private function applyDefaultResolutionType(array $mapping): array
    {
        return array_map(function ($value) {
            return $value instanceof ResolvedClass ? $value : new Singleton($value);
        }, $mapping);
    }
}
