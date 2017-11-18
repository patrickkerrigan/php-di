<?php

namespace Pkerrigan\Di\Exception;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

/**
 * Lightweight dependency injector
 * 3 Clause BSD Licence
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 13/03/16
 */
class InstantiationException extends RuntimeException implements ContainerExceptionInterface
{
}
