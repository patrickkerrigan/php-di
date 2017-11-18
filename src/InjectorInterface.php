<?php

namespace Pkerrigan\Di;

use Psr\Container\ContainerInterface;

/**
 * Lightweight dependency injector
 * 3 Clause BSD Licence
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 13/03/16
 */
interface InjectorInterface extends ContainerInterface
{
    public function addClassResolver(ClassResolver $resolver): void;
}
