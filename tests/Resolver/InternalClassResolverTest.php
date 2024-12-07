<?php

namespace Pkerrigan\Di\Resolver;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Pkerrigan\Di\Injector;
use Pkerrigan\Di\InjectorInterface;
use Pkerrigan\Di\ResolvedClass\Singleton;
use Psr\Container\ContainerInterface;

class InternalClassResolverTest extends TestCase
{
    #[Test]
    public function GivenInjectorInterfaceName_WhenCalled_ReturnsInjectorAsSingleton(): void
    {
        $resolver = new InternalClassResolver();

        self::assertEquals(
            new Singleton(Injector::class),
            $resolver->resolveConcreteClass(InjectorInterface::class)
        );
    }

    #[Test]
    public function GivenContainerInterfaceName_WhenCalled_ReturnsInjectorAsSingleton(): void
    {
        $resolver = new InternalClassResolver();

        self::assertEquals(
            new Singleton(Injector::class),
            $resolver->resolveConcreteClass(ContainerInterface::class)
        );
    }

    #[Test]
    public function GivenNonInternalInterfaceName_WhenCalled_ReturnsNull(): void
    {
        $resolver = new InternalClassResolver();

        self::assertNull($resolver->resolveConcreteClass('TestInterface'));
    }
}
