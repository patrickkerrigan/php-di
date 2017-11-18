<?php

namespace Pkerrigan\Di\Resolver;

use PHPUnit\Framework\TestCase;
use Pkerrigan\Di\Injector;
use Pkerrigan\Di\InjectorInterface;
use Pkerrigan\Di\ResolvedClass\Singleton;
use Psr\Container\ContainerInterface;

class InternalClassResolverTest extends TestCase
{
    /**
     * @test
     */
    public function GivenInjectorInterfaceName_WhenCalled_ReturnsInjectorAsSingleton()
    {
        $resolver = new InternalClassResolver();

        $this->assertEquals(
            new Singleton(Injector::class),
            $resolver->resolveConcreteClass(InjectorInterface::class)
        );
    }

    /**
     * @test
     */
    public function GivenContainerInterfaceName_WhenCalled_ReturnsInjectorAsSingleton()
    {
        $resolver = new InternalClassResolver();

        $this->assertEquals(
            new Singleton(Injector::class),
            $resolver->resolveConcreteClass(ContainerInterface::class)
        );
    }

    /**
     * @test
     */
    public function GivenNonInternalInterfaceName_WhenCalled_ReturnsNull()
    {
        $resolver = new InternalClassResolver();

        $this->assertNull($resolver->resolveConcreteClass('TestInterface'));
    }
}
