<?php

namespace Pkerrigan\Di\Resolver;

use Pkerrigan\Di\ClassResolver;
use Pkerrigan\Di\Injector;
use Pkerrigan\Di\InjectorInterface;
use Pkerrigan\Di\ResolvedClass;
use Pkerrigan\Di\ResolvedClass\Singleton;
use Psr\Container\ContainerInterface;

/**
 * Lightweight dependency injector
 * 3 Clause BSD Licence
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 13/03/16
 */
final readonly class InternalClassResolver implements ClassResolver
{
    private array $internalMappings;

    public function __construct()
    {
        $this->internalMappings = [
            InjectorInterface::class => new Singleton(Injector::class),
            ContainerInterface::class => new Singleton(Injector::class)
        ];
    }

    public function resolveConcreteClass(string $className): ?ResolvedClass
    {
        return $this->internalMappings[$className] ?? null;
    }
}
