<?php

namespace Pkerrigan\Di\ResolvedClass;

use Pkerrigan\Di\ResolvedClass;

/**
 * Lightweight dependency injector
 * 3 Clause BSD Licence
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 24/02/2018
 */
class Factory implements ResolvedClass
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $method;

    public function __construct(string $name, string $method = 'get')
    {
        $this->name = $name;
        $this->method = $method;
    }

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
}
