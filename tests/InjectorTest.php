<?php

namespace Pkerrigan\Di;

use PHPUnit\Framework\TestCase;
use Pkerrigan\Di\ResolvedClass\Factory;
use Pkerrigan\Di\ResolvedClass\Prototype;
use Pkerrigan\Di\ResolvedClass\Singleton;
use Pkerrigan\Di\Resolver\ArrayMapClassResolver;

class InjectorTest extends TestCase
{
    /**
     * @test
     */
    public function GivenInjector_WhenGetInstanceCalled_ReturnsSingletonInstance()
    {
        $instance = Injector::getInstance();
        $instance2 = Injector::getInstance();

        $this->assertInstanceOf(Injector::class, $instance);
        $this->assertEquals(spl_object_hash($instance), spl_object_hash($instance2));
    }

    /**
     * @test
     */
    public function GivenInjector_WhenHasCalledWithUnknownClass_ReturnsFalse()
    {
        $injector = new Injector();
        $this->assertFalse($injector->has('TestInterface'));
    }

    /**
     * @test
     */
    public function GivenInjector_WhenHasCalledWithKnownClass_ReturnsTrue()
    {
        $injector = new Injector();
        $this->assertTrue($injector->has(InjectorInterface::class));
    }

    /**
     * @test
     * @expectedException \Pkerrigan\Di\Exception\NotFoundException
     */
    public function GivenInjector_WhenGetCalledWithUnknownClass_ThrowsNotFoundException()
    {
        $injector = new Injector();
        $injector->get('TestInterface');
    }

    /**
     * @test
     * @expectedException \Pkerrigan\Di\Exception\InstantiationException
     */
    public function GivenInjector_WhenGetCalledWithAbstractClass_ThrowsInstantiationException()
    {
        $injector = new Injector();
        $injector->get(AbstractClass::class);
    }

    /**
     * @test
     */
    public function GivenInjector_WhenGetCalledOnClassWithNoConstructor_InstantiatesClass()
    {
        $injector = new Injector();
        $this->assertInstanceOf(ClassWithNoConstructor::class, $injector->get(ClassWithNoConstructor::class));
    }

    /**
     * @test
     */
    public function GivenInjector_WhenGetCalledOnClassWithConstructor_InstantiatesClass()
    {
        $injector = new Injector();
        $this->assertInstanceOf(ClassWithConstructor::class, $injector->get(ClassWithConstructor::class));
    }

    /**
     * @test
     */
    public function GivenInjector_WhenGetCalledOnClassWithDependency_InstantiatesClassWithDependency()
    {
        $injector = new Injector();
        $classWithDependency = $injector->get(ClassWithDependency::class);
        $this->assertInstanceOf(ClassWithDependency::class, $classWithDependency);
        $this->assertInstanceOf(Dependency::class, $classWithDependency->dependency);
    }

    /**
     * @test
     */
    public function GivenInjector_WhenGetInstanceCalledForSingleton_ReturnsSingletonInstance()
    {
        $injector = new Injector();
        $injector->addClassResolver(new ArrayMapClassResolver([
            ClassWithNoConstructor::class => new Singleton(ClassWithNoConstructor::class)
        ]));

        $instance = $injector->get(ClassWithNoConstructor::class);
        $instance2 = $injector->get(ClassWithNoConstructor::class);

        $this->assertInstanceOf(ClassWithNoConstructor::class, $instance);
        $this->assertEquals(spl_object_hash($instance), spl_object_hash($instance2));
    }

    /**
     * @test
     */
    public function GivenInjector_WhenGetInstanceCalledForPrototype_ReturnsPrototypeInstance()
    {
        $injector = new Injector();
        $injector->addClassResolver(new ArrayMapClassResolver([
            ClassWithNoConstructor::class => new Prototype(ClassWithNoConstructor::class)
        ]));

        $instance = $injector->get(ClassWithNoConstructor::class);
        $instance2 = $injector->get(ClassWithNoConstructor::class);

        $this->assertInstanceOf(ClassWithNoConstructor::class, $instance);
        $this->assertNotEquals(spl_object_hash($instance), spl_object_hash($instance2));
    }

    /**
     * @test
     */
    public function GivenInjector_WhenGetInstanceCalledForFactory_ReturnsObjectFromFactory()
    {
        $injector = new Injector();
        $injector->addClassResolver(new ArrayMapClassResolver([
            ClassWithNoConstructor::class => new Factory(ObjectFactory::class)
        ]));

        $instance = $injector->get(ClassWithNoConstructor::class);
        $instance2 = $injector->get(ClassWithNoConstructor::class);

        $this->assertInstanceOf(ClassWithNoConstructor::class, $instance);
        $this->assertInstanceOf(ClassWithNoConstructor::class, $instance2);
        $this->assertNotEquals(spl_object_hash($instance), spl_object_hash($instance2));
    }

    /**
     * @test
     */
    public function GivenInjector_WhenGetInstanceCalledForFactoryWithCustomMethod_ReturnsObjectFromFactory()
    {
        $injector = new Injector();
        $injector->addClassResolver(new ArrayMapClassResolver([
            ClassWithConstructor::class => new Factory(ObjectFactory::class, 'getClassWithConstructor')
        ]));

        $instance = $injector->get(ClassWithConstructor::class);
        $instance2 = $injector->get(ClassWithConstructor::class);

        $this->assertInstanceOf(ClassWithConstructor::class, $instance);
        $this->assertInstanceOf(ClassWithConstructor::class, $instance2);
        $this->assertNotEquals(spl_object_hash($instance), spl_object_hash($instance2));
    }
}
