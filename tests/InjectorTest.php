<?php

namespace Pkerrigan\Di;

use DateTime;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Pkerrigan\Di\Exception\InstantiationException;
use Pkerrigan\Di\Exception\NotFoundException;
use Pkerrigan\Di\ResolvedClass\EagerPrototype;
use Pkerrigan\Di\ResolvedClass\EagerSingleton;
use Pkerrigan\Di\ResolvedClass\Factory;
use Pkerrigan\Di\ResolvedClass\Prototype;
use Pkerrigan\Di\ResolvedClass\Singleton;
use Pkerrigan\Di\Resolver\ArrayMapClassResolver;
use ReflectionClass;

class InjectorTest extends TestCase
{
    #[Test]
    public function GivenInjector_WhenGetInstanceCalled_ReturnsSingletonInstance(): void
    {
        $instance = Injector::getInstance();
        $instance2 = Injector::getInstance();

        self::assertInstanceOf(Injector::class, $instance);
        self::assertEquals(spl_object_hash($instance), spl_object_hash($instance2));
    }

    #[Test]
    public function GivenInjector_WhenHasCalledWithUnknownClass_ReturnsFalse(): void
    {
        $injector = new Injector();
        self::assertFalse($injector->has('TestInterface'));
    }

    #[Test]
    public function GivenInjector_WhenHasCalledWithKnownClass_ReturnsTrue(): void
    {
        $injector = new Injector();
        self::assertTrue($injector->has(InjectorInterface::class));
    }

    #[Test]
    public function GivenInjector_WhenGetCalledWithUnknownClass_ThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);

        $injector = new Injector();
        $injector->get('TestInterface');
    }

    #[Test]
    public function GivenInjector_WhenGetCalledWithAbstractClass_ThrowsInstantiationException(): void
    {
        $this->expectException(InstantiationException::class);

        $injector = new Injector();
        $injector->get(AbstractClass::class);
    }

    #[Test]
    public function GivenInjector_WhenGetCalledOnClassWithNoConstructor_InstantiatesClass(): void
    {
        $injector = new Injector();
        self::assertInstanceOf(ClassWithNoConstructor::class, $injector->get(ClassWithNoConstructor::class));
    }

    #[Test]
    public function GivenInjector_WhenGetCalledOnClassWithConstructor_InstantiatesClass(): void
    {
        $injector = new Injector();
        self::assertInstanceOf(ClassWithConstructor::class, $injector->get(ClassWithConstructor::class));
    }

    #[Test]
    public function GivenInjector_WhenGetCalledOnClassWithDependency_InstantiatesClassWithDependency(): void
    {
        $injector = new Injector();
        $classWithDependency = $injector->get(ClassWithDependency::class);
        self::assertInstanceOf(ClassWithDependency::class, $classWithDependency);
        self::assertInstanceOf(Dependency::class, $classWithDependency->dependency);
    }

    #[Test]
    public function GivenInjector_WhenGetCalledOnClassWithDependencyAndBuiltInType_InstantiatesClassWithDependency(): void
    {
        $injector = new Injector();
        $classWithBuiltInType = $injector->get(ClassWithBuiltInType::class);
        self::assertInstanceOf(ClassWithBuiltInType::class, $classWithBuiltInType);
        self::assertInstanceOf(Dependency::class, $classWithBuiltInType->dependency);
    }

    #[Test]
    public function GivenInjector_WhenGetInstanceCalledForSingleton_ReturnsSingletonInstance(): void
    {
        $injector = new Injector();
        $injector->addClassResolver(new ArrayMapClassResolver([
            ClassWithDependency::class => new Singleton(ClassWithDependency::class)
        ]));

        $instance = $injector->get(ClassWithDependency::class);
        $instance2 = $injector->get(ClassWithDependency::class);

        self::assertInstanceOf(ClassWithDependency::class, $instance);
        self::assertEquals(spl_object_hash($instance), spl_object_hash($instance2));

        $reflector = new ReflectionClass(ClassWithDependency::class);
        self:self::assertTrue($reflector->isUninitializedLazyObject($instance));
    }

    #[Test]
    public function GivenInjector_WhenGetInstanceCalledForPrototype_ReturnsPrototypeInstance(): void
    {
        $injector = new Injector();
        $injector->addClassResolver(new ArrayMapClassResolver([
            ClassWithDependency::class => new Prototype(ClassWithDependency::class)
        ]));

        $instance = $injector->get(ClassWithDependency::class);
        $instance2 = $injector->get(ClassWithDependency::class);

        self::assertInstanceOf(ClassWithDependency::class, $instance);
        self::assertNotEquals(spl_object_hash($instance), spl_object_hash($instance2));

        $reflector = new ReflectionClass(ClassWithDependency::class);
        self:self::assertTrue($reflector->isUninitializedLazyObject($instance));
    }

    #[Test]
    public function GivenInjector_WhenGetInstanceCalledForEagerSingleton_ReturnsSingletonInstance(): void
    {
        $injector = new Injector();
        $injector->addClassResolver(new ArrayMapClassResolver([
            ClassWithDependency::class => new EagerSingleton(ClassWithDependency::class)
        ]));

        $instance = $injector->get(ClassWithDependency::class);
        $instance2 = $injector->get(ClassWithDependency::class);

        self::assertInstanceOf(ClassWithDependency::class, $instance);
        self::assertEquals(spl_object_hash($instance), spl_object_hash($instance2));

        $reflector = new ReflectionClass(ClassWithDependency::class);
        self::assertFalse($reflector->isUninitializedLazyObject($instance));
    }

    #[Test]
    public function GivenInjector_WhenGetInstanceCalledForEagerPrototype_ReturnsPrototypeInstance(): void
    {
        $injector = new Injector();
        $injector->addClassResolver(new ArrayMapClassResolver([
            ClassWithDependency::class => new EagerPrototype(ClassWithDependency::class)
        ]));

        $instance = $injector->get(ClassWithDependency::class);
        $instance2 = $injector->get(ClassWithDependency::class);

        self::assertInstanceOf(ClassWithDependency::class, $instance);
        self::assertNotEquals(spl_object_hash($instance), spl_object_hash($instance2));

        $reflector = new ReflectionClass(ClassWithDependency::class);
        self::assertFalse($reflector->isUninitializedLazyObject($instance));
    }

    #[Test]
    public function GivenInjector_WhenGetInstanceCalledForFactory_ReturnsObjectFromFactory(): void
    {
        $injector = new Injector();
        $injector->addClassResolver(new ArrayMapClassResolver([
            ClassWithNoConstructor::class => new Factory(ObjectFactory::class)
        ]));

        $instance = $injector->get(ClassWithNoConstructor::class);
        $instance2 = $injector->get(ClassWithNoConstructor::class);

        self::assertInstanceOf(ClassWithNoConstructor::class, $instance);
        self::assertInstanceOf(ClassWithNoConstructor::class, $instance2);
        self::assertNotEquals(spl_object_hash($instance), spl_object_hash($instance2));
    }

    #[Test]
    public function GivenInjector_WhenGetInstanceCalledForFactoryWithCustomMethod_ReturnsObjectFromFactory(): void
    {
        $injector = new Injector();
        $injector->addClassResolver(new ArrayMapClassResolver([
            ClassWithConstructor::class => new Factory(ObjectFactory::class, 'getClassWithConstructor')
        ]));

        $instance = $injector->get(ClassWithConstructor::class);
        $instance2 = $injector->get(ClassWithConstructor::class);

        self::assertInstanceOf(ClassWithConstructor::class, $instance);
        self::assertInstanceOf(ClassWithConstructor::class, $instance2);
        self::assertNotEquals(spl_object_hash($instance), spl_object_hash($instance2));
    }

    #[Test]
    public function GivenInjector_WhenGetCalledOnInternalClass_InstantiatesClassEagerly(): void
    {
        $injector = new Injector();
        $object = $injector->get(DateTime::class);

        self::assertInstanceOf(DateTime::class, $object);

        $reflector = new ReflectionClass(DateTime::class);

        self::assertFalse($reflector->isUninitializedLazyObject($object));
    }
}
