<?php

namespace Pkerrigan\Di\ResolvedClass;

/**
 * Lightweight dependency injector
 * 3 Clause BSD Licence
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 13/03/16
 */
readonly class Prototype extends AbstractLazyInstance
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
