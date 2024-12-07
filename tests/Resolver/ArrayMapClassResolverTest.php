<?php

namespace Pkerrigan\Di\Resolver;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Pkerrigan\Di\ResolvedClass\Prototype;
use Pkerrigan\Di\ResolvedClass\Singleton;

class ArrayMapClassResolverTest extends TestCase
{
    const array DEFAULT_CLASS_MAPPING = [
        'TestInterface1' => 'ConcreteClass1',
        'TestInterface2' => 'ConcreteClass2',
        'TestInterface3' => 'ConcreteClass2'
    ];

    #[Test]
    public function GivenEmptyMapping_WhenCalled_ReturnsNull(): void
    {
        $resolver = new ArrayMapClassResolver([]);

        self::assertNull($resolver->resolveConcreteClass('TestInterface'));
    }

    #[Test]
    public function GivenDefaultMapping_WhenCalled_ReturnsMappedClassNameAsSingleton(): void
    {
        $resolver = new ArrayMapClassResolver(self::DEFAULT_CLASS_MAPPING);

        foreach (self::DEFAULT_CLASS_MAPPING as $interface => $mappedClass) {
            self::assertEquals(new Singleton($mappedClass), $resolver->resolveConcreteClass($interface));
        }
    }

    #[Test]
    public function GivenMappingWithPrototype_WhenCalled_ReturnsMappedClassNameAsPrototype(): void
    {
        $resolver = new ArrayMapClassResolver(['TestInterface1' => new Prototype('TestInterface1')]);

        self::assertEquals(
            new Prototype('TestInterface1'),
            $resolver->resolveConcreteClass('TestInterface1')
        );
    }

    #[Test]
    public function GivenMapping_WhenCalledWithUnknownInterfaceName_ReturnsNull(): void
    {
        $resolver = new ArrayMapClassResolver(self::DEFAULT_CLASS_MAPPING);
        self::assertNull($resolver->resolveConcreteClass('TestInterface4'));
    }
}
