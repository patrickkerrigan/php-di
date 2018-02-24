<?php

namespace Pkerrigan\Di\ResolvedClass;

use PHPUnit\Framework\TestCase;
use Pkerrigan\Di\ObjectFactory;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 24/02/2018
 */
class FactoryTest extends TestCase
{
    /**
     * @test
     * Currently there's no support for caching the return value of factories,
     * so this should always be false
     */
    public function GivenFactory_WhenShouldBeCachedCalled_ReturnsFalse()
    {
        $factory = new Factory(ObjectFactory::class);

        $this->assertFalse($factory->shouldBeCached());
    }
}
