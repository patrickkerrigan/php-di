<?php

namespace Pkerrigan\Di\Resolver;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Pkerrigan\Di\ClassWithNoConstructor;
use Pkerrigan\Di\ResolvedClass\Singleton;

class PassthroughClassResolverTest extends TestCase
{
    #[Test]
    public function GivenClassName_WhenCalled_ReturnsSingletonClass(): void
    {
        $resolver = new PassthroughClassResolver();
        $className = ClassWithNoConstructor::class;
        self::assertEquals(new Singleton($className), $resolver->resolveConcreteClass($className));
    }

    #[Test]
    public function GivenInvalidClassName_WhenCalled_ReturnsNull(): void
    {
        $resolver = new PassthroughClassResolver();
        $className = "InvalidClass";
        self::assertNull($resolver->resolveConcreteClass($className));
    }
}
