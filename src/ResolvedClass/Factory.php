<?php

namespace Pkerrigan\Di\ResolvedClass;

use Pkerrigan\Di\ResolvedClass;

/**
 * Lightweight dependency injector
 * 3 Clause BSD Licence
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 24/02/2018
 */
readonly class Factory implements ResolvedClass
{
    public function __construct(
        private string $name,
        private string $method = 'get'
    ) {}

    public function getClassName(): string
    {
        return $this->name;
    }

    public function shouldBeCached(): bool
    {
        return false;
    }

    public function getFactoryMethod(): ?string
    {
        return $this->method;
    }

    public function isLazy(): bool
    {
        return false;
    }
}
