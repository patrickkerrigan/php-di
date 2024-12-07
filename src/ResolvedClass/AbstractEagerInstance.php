<?php

declare(strict_types=1);

namespace Pkerrigan\Di\ResolvedClass;

/**
 * Lightweight dependency injector
 * 3 Clause BSD Licence
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 07/12/24
 */
abstract readonly class AbstractEagerInstance extends AbstractInstance
{
    public function isLazy(): bool
    {
        return false;
    }
}