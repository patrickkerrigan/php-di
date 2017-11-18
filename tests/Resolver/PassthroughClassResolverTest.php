<?php

namespace Pkerrigan\Di\Resolver;

use PHPUnit\Framework\TestCase;
use Pkerrigan\Di\ClassWithNoConstructor;
use Pkerrigan\Di\ResolvedClass\Singleton;

class PassthroughClassResolverTest extends TestCase
{
    /**
     * @test
     */
    public function GivenClassName_WhenCalled_ReturnsSingletonClass()
    {
        $resolver = new PassthroughClassResolver();
        $className = ClassWithNoConstructor::class;
        $this->assertEquals(new Singleton($className), $resolver->resolveConcreteClass($className));
    }

    /**
     * @test
     */
    public function GivenInvalidClassName_WhenCalled_ReturnsNull()
    {
        $resolver = new PassthroughClassResolver();
        $className = "InvalidClass";
        $this->assertNull($resolver->resolveConcreteClass($className));
    }
}
