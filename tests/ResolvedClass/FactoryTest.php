<?php

namespace Pkerrigan\Di\ResolvedClass;

use PHPUnit\Framework\Attributes\Test;
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
     * Currently there's no support for caching the return value of factories,
     * so this should always be false
     */
    #[Test]
    public function GivenFactory_WhenShouldBeCachedCalled_ReturnsFalse(): void
    {
        self::assertFalse(new Factory(ObjectFactory::class)->shouldBeCached());
    }

    #[Test]
    public function GivenFactory_WhenIsLazyCalled_ReturnsFalse(): void
    {
        self::assertFalse(new Factory(ObjectFactory::class)->isLazy());
    }
}
