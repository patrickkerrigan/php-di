<?php

namespace Pkerrigan\Di;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 24/02/2018
 */
class ObjectFactory
{
    public function get(): ClassWithNoConstructor
    {
        return new ClassWithNoConstructor();
    }

    public function getClassWithConstructor(): ClassWithConstructor
    {
        return new ClassWithConstructor();
    }
}
