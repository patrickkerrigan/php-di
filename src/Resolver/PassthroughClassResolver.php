<?php

namespace Pkerrigan\Di\Resolver;

use Pkerrigan\Di\ClassResolver;
use Pkerrigan\Di\ResolvedClass;
use Pkerrigan\Di\ResolvedClass\Singleton;

use function class_exists;

/**
 * Lightweight dependency injector
 * 3 Clause BSD Licence
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 13/03/16
 */
final readonly class PassthroughClassResolver implements ClassResolver
{
    public function resolveConcreteClass(string $className): ?ResolvedClass
    {
        return class_exists($className) ? new Singleton($className) : null;
    }
}
