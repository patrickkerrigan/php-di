<?php

namespace Pkerrigan\Di\Resolver;

use Pkerrigan\Di\ClassResolver;
use Pkerrigan\Di\ResolvedClass;
use Pkerrigan\Di\ResolvedClass\Singleton;

use function array_map;

/**
 * Lightweight dependency injector
 * 3 Clause BSD Licence
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 13/03/16
 */
final readonly class ArrayMapClassResolver implements ClassResolver
{
    private array $mapping;

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
