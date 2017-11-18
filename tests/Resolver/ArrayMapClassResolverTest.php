<?php

namespace Pkerrigan\Di\Resolver;

use PHPUnit\Framework\TestCase;
use Pkerrigan\Di\ResolvedClass\Prototype;
use Pkerrigan\Di\ResolvedClass\Singleton;

class ArrayMapClassResolverTest extends TestCase
{
    const DEFAULT_CLASS_MAPPING = [
        'TestInterface1' => 'ConcreteClass1',
        'TestInterface2' => 'ConcreteClass2',
        'TestInterface3' => 'ConcreteClass2'
    ];

    /**
     * @test
     */
    public function GivenEmptyMapping_WhenCalled_ReturnsNull()
    {
        $resolver = new ArrayMapClassResolver([]);

        $this->assertNull($resolver->resolveConcreteClass('TestInterface'));
    }

    /**
     * @test
     */
    public function GivenDefaultMapping_WhenCalled_ReturnsMappedClassNameAsSingleton()
    {
        $resolver = new ArrayMapClassResolver(self::DEFAULT_CLASS_MAPPING);

        foreach (self::DEFAULT_CLASS_MAPPING as $interface => $mappedClass) {
            $this->assertEquals(new Singleton($mappedClass), $resolver->resolveConcreteClass($interface));
        }
    }

    /**
     * @test
     */
    public function GivenMappingWithPrototype_WhenCalled_ReturnsMappedClassNameAsPrototype()
    {
        $resolver = new ArrayMapClassResolver(['TestInterface1' => new Prototype('TestInterface1')]);

        $this->assertEquals(
            new Prototype('TestInterface1'),
            $resolver->resolveConcreteClass('TestInterface1')
        );
    }

    /**
     * @test
     */
    public function GivenMapping_WhenCalledWithUnknownInterfaceName_ReturnsNull()
    {
        $resolver = new ArrayMapClassResolver(self::DEFAULT_CLASS_MAPPING);
        $this->assertNull($resolver->resolveConcreteClass('TestInterface4'));
    }
}
