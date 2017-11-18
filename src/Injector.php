<?php

namespace Pkerrigan\Di;

use Pkerrigan\Di\Exception\InstantiationException;
use Pkerrigan\Di\Exception\NotFoundException;
use Pkerrigan\Di\Resolver\InternalClassResolver;
use Pkerrigan\Di\Resolver\PassthroughClassResolver;
use ReflectionClass;
use ReflectionException;

/**
 * Lightweight dependency injector
 * 3 Clause BSD Licence
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 13/03/16
 */
class Injector implements InjectorInterface
{
    protected static $instance = null;
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
        if (is_null(self::$instance)) {
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
    public function has($className)
    {
        return !is_null($this->resolveClass($className));
    }

    /**
     * @param string $className
     * @return object
     */
    public function get($className)
    {
        $resolvedClass = $this->resolveClass($className);

        if (is_null($resolvedClass)) {
            throw new NotFoundException("Class {$className} could not be resolved");
        }

        if (!is_null($singleton = $this->getCachedSingleton($resolvedClass->getClassName()))) {
            return $singleton;
        }

        return $this->constructNewInstance($resolvedClass);
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
            if (is_null($concreteClass = $resolver->resolveConcreteClass($className))) {
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

        if (is_null($constructor)) {
            return [];
        }

        $constructorParameters = $constructor->getParameters();

        $constructorArguments = [];
        foreach ($constructorParameters as $parameter) {
            $paramClass = $parameter->getClass();

            if (is_null($paramClass)) {
                break;
            }

            $constructorArguments[] = $this->get($paramClass->getName());
        }

        return $constructorArguments;
    }

    /**
     * @param ResolvedClass $resolvedClass
     * @return object
     */
    private function constructNewInstance(ResolvedClass $resolvedClass)
    {
        try {
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
        } catch (ReflectionException $e) {
            throw new InstantiationException("Unable to load class '{$resolvedClass->getClassName()}'");
        }
    }

    /**
     * @param string $className
     * @return object|null
     */
    private function getCachedSingleton(string $className)
    {
        return $this->cachedSingletons[$className] ?? null;
    }
}
