<?php
namespace Pkerrigan\Di;

class ClassWithDependency
{
    public $dependency;

    public function __construct(Dependency $dependency)
    {
        $this->dependency = $dependency;
    }
}
