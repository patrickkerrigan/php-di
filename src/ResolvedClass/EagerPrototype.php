<?php

namespace Pkerrigan\Di\ResolvedClass;

/**
 * Lightweight dependency injector
 * 3 Clause BSD Licence
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 07/12/24
 */
readonly class EagerPrototype extends AbstractEagerInstance
{
    public function __construct(private string $name)
    {}

    public function getClassName(): string
    {
        return $this->name;
    }

    public function shouldBeCached(): bool
    {
        return false;
    }
}
