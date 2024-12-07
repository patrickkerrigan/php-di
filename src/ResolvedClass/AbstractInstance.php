<?php

namespace Pkerrigan\Di\ResolvedClass;

use Pkerrigan\Di\ResolvedClass;

/**
 * Lightweight dependency injector
 * 3 Clause BSD Licence
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 24/02/2018
 */
abstract readonly class AbstractInstance implements ResolvedClass
{
    /**
     * Get the method to call on this class to return a real instance
     * @return null|string
     */
    public function getFactoryMethod(): ?string
    {
        return null;
    }
}
