<?php

namespace Pkerrigan\Di;

/**
 * Lightweight dependency injector
 * 3 Clause BSD Licence
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 13/03/16
 */
interface ResolvedClass
{
    /**
     * Get the resolved class name of this result
     * @return string
     */
    public function getClassName(): string;

    /**
     * Should this result be cached (i.e: as a singleton)
     * @return bool
     */
    public function shouldBeCached(): bool;

    /**
     * Get the method to call on this class to return a real instance
     * @return null|string
     */
    public function getFactoryMethod(): ?string;
}
