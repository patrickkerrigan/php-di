<?php

declare(strict_types=1);

namespace Pkerrigan\Di;

class ClassWithBuiltInType
{
    public $dependency;

    public function __construct(Dependency $dependency, ?string $s = null)
    {
        $this->dependency = $dependency;
    }
}
