<?php

declare(strict_types=1);

namespace ResolvedClass;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Pkerrigan\Di\ClassWithNoConstructor;
use Pkerrigan\Di\ResolvedClass\EagerPrototype;
use Pkerrigan\Di\ResolvedClass\EagerSingleton;
use Pkerrigan\Di\ResolvedClass\Prototype;
use Pkerrigan\Di\ResolvedClass\Singleton;

class LazinessTest extends TestCase
{
    #[Test]
    public function GivenLazyResolvedClass_WhenIsLazyCalled_ReturnsTrue(): void
    {
        self::assertTrue(new Prototype(ClassWithNoConstructor::class)->isLazy());
        self::assertTrue(new Singleton(ClassWithNoConstructor::class)->isLazy());
    }

    #[Test]
    public function GivenEagerResolvedClass_WhenIsLazyCalled_ReturnsFalse(): void
    {
        self::assertFalse(new EagerPrototype(ClassWithNoConstructor::class)->isLazy());
        self::assertFalse(new EagerSingleton(ClassWithNoConstructor::class)->isLazy());
    }
}