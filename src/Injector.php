<?php

namespace Pkerrigan\Di;

use Pkerrigan\Di\Exception\InstantiationException;
use Pkerrigan\Di\Exception\NotFoundException;
use Pkerrigan\Di\Resolver\InternalClassResolver;
use Pkerrigan\Di\Resolver\PassthroughClassResolver;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;

/**
 * Lightweight dependency injector
 * 3 Clause BSD Licence
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 13/03/16
 */
class Injector implements InjectorInterface
{
    protected static $instance;
    /**
     * @var ClassResolver[]
     */
    private $classResolvers;
    /**
     * @var array
     */
    private $cachedSingletons;

    /**
     * Get an instance of this class
     * @return static
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function __construct()
    {
        $this->classResolvers = [
            new InternalClassResolver(),
            new PassthroughClassResolver()
        ];

        $this->cachedSingletons = [
            static::class => $this
        ];
    }

    /**
     * @param string $className
     * @return bool
     */
    public function has($className): bool
    {
        return $this->resolveClass($className) !== null;
    }

    /**
     * @param string $className
     * @return object
     */
    public function get($className)
    {
        $resolvedClass = $this->resolveClass($className);

        if ($resolvedClass === null) {
            throw new NotFoundException("Class {$className} could not be resolved");
        }

        if (($singleton = $this->getCachedSingleton($resolvedClass)) !== null) {
            return $singleton;
        }

        return $this->getNewInstance($resolvedClass);
    }

    /**
     * @param ClassResolver $resolver
     */
    public function addClassResolver(ClassResolver $resolver): void
    {
        array_unshift($this->classResolvers, $resolver);
    }

    /**
     * @param string $className
     * @return ResolvedClass|null
     */
    private function resolveClass(string $className): ?ResolvedClass
    {
        foreach ($this->classResolvers as $resolver) {
            if (($concreteClass = $resolver->resolveConcreteClass($className)) === null) {
                continue;
            }

            return $concreteClass;
        }

        return null;
    }

    /**
     * @param $reflectionClass
     * @return array
     */
    private function constructDependencies(ReflectionClass $reflectionClass): array
    {
        $constructor = $reflectionClass->getConstructor();

        if ($constructor === null) {
            return [];
        }

        $constructorParameters = $constructor->getParameters();

        $constructorArguments = [];
        foreach ($constructorParameters as $parameter) {
            $paramType = $parameter->getType();

            if (!$paramType instanceof ReflectionNamedType || $paramType->isBuiltin()) {
                break;
            }

            $constructorArguments[] = $this->get($paramType->getName());
        }

        return $constructorArguments;
    }

    /**
     * @param ResolvedClass $resolvedClass
     * @return object
     */
    private function getNewInstance(ResolvedClass $resolvedClass)
    {
        if (($factoryMethod = $resolvedClass->getFactoryMethod()) !== null) {
            return $this->get($resolvedClass->getClassName())->{$factoryMethod}();
        }

        try {
            return $this->construct($resolvedClass);
        } catch (ReflectionException $e) {
            throw new InstantiationException("Unable to load class '{$resolvedClass->getClassName()}'");
        }
    }

    /**
     * @param ResolvedClass $resolvedClass
     * @return object|null
     */
    private function getCachedSingleton(ResolvedClass $resolvedClass)
    {
        if (!$resolvedClass->shouldBeCached()) {
            return null;
        }

        return $this->cachedSingletons[$resolvedClass->getClassName()] ?? null;
    }

    /**
     * @param ResolvedClass $resolvedClass
     * @return object
     * @throws ReflectionException
     */
    private function construct(ResolvedClass $resolvedClass)
    {
        $reflectionClass = new ReflectionClass($resolvedClass->getClassName());

        if (!$reflectionClass->isInstantiable()) {
            throw new InstantiationException("Unable to instantiate class '{$resolvedClass->getClassName()}'");
        }

        $dependencies = $this->constructDependencies($reflectionClass);

        $instance = $reflectionClass->newInstanceArgs($dependencies);
        if ($resolvedClass->shouldBeCached()) {
            $this->cachedSingletons[$resolvedClass->getClassName()] = $instance;
        }

        return $instance;
    }
}
