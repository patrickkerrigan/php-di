<?php

namespace Pkerrigan\Di;

/**
 * Lightweight dependency injector
 * 3 Clause BSD Licence
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 13/03/16
 */
interface ClassResolver
{
    public function resolveConcreteClass(string $className): ?ResolvedClass;
}
