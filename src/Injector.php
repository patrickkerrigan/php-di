<?php

namespace Pkerrigan\Di;

use Pkerrigan\Di\Exception\InstantiationException;
use Pkerrigan\Di\Exception\NotFoundException;
use Pkerrigan\Di\Resolver\InternalClassResolver;
use Pkerrigan\Di\Resolver\PassthroughClassResolver;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use SplStack;

/**
 * Lightweight dependency injector
 * 3 Clause BSD Licence
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 13/03/16
 */
final class Injector implements InjectorInterface
{
    private static ?self $instance = null;

    /**
     * @var SplStack<ClassResolver>
     */
    private SplStack $classResolvers;
    /**
     * @var object[]
     */
    private array $cachedSingletons;

    /**
     * Get an instance of this class
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
        $this->classResolvers = new SplStack();
        $this->classResolvers->push(new PassthroughClassResolver());
        $this->classResolvers->push(new InternalClassResolver());

        $this->cachedSingletons = [
            static::class => $this
        ];
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return $this->resolveClass($id) !== null;
    }

    /**
     * @param string $id
     * @return object
     */
    public function get(string $id): object
    {
        $resolvedClass = $this->resolveClass($id);

        if ($resolvedClass === null) {
            throw new NotFoundException("Class $id could not be resolved");
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
        $this->classResolvers->push($resolver);
    }

    /**
     * @param string $className
     * @return ResolvedClass|null
     */
    private function resolveClass(string $className): ?ResolvedClass
    {
        foreach ($this->classResolvers as $resolver) {
            if (($concreteClass = $resolver->resolveConcreteClass($className)) !== null) {
                return $concreteClass;
            }
        }

        return null;
    }

    private function constructDependencies(ReflectionMethod $constructor): array
    {
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
    private function getNewInstance(ResolvedClass $resolvedClass): object
    {
        if (($factoryMethod = $resolvedClass->getFactoryMethod()) !== null) {
            return $this->get($resolvedClass->getClassName())->{$factoryMethod}();
        }

        try {
            return $this->construct($resolvedClass);
        } catch (ReflectionException) {
            throw new InstantiationException("Unable to load class '{$resolvedClass->getClassName()}'");
        }
    }

    /**
     * @param ResolvedClass $resolvedClass
     * @return object|null
     */
    private function getCachedSingleton(ResolvedClass $resolvedClass): ?object
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
    private function construct(ResolvedClass $resolvedClass): object
    {
        $reflectionClass = new ReflectionClass($resolvedClass->getClassName());

        if (!$reflectionClass->isInstantiable()) {
            throw new InstantiationException("Unable to instantiate class '{$resolvedClass->getClassName()}'");
        }

        $instance = match (($resolvedClass->isLazy() && !$reflectionClass->isInternal())) {
            true => $reflectionClass->newLazyGhost(function (object $x) use ($reflectionClass): void {
                if (($constructor = $reflectionClass->getConstructor()) !== null) {
                    $x->__construct(...$this->constructDependencies($constructor));
                }
            }),

            false => $reflectionClass->newInstanceArgs(
                (($constructor = $reflectionClass->getConstructor()) !== null)
                    ? $this->constructDependencies($constructor)
                    : []
            )
        };

        if ($resolvedClass->shouldBeCached()) {
            $this->cachedSingletons[$resolvedClass->getClassName()] = $instance;
        }

        return $instance;
    }
}
