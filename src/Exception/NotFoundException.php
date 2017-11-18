<?php

namespace Pkerrigan\Di\Exception;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

/**
 * Lightweight dependency injector
 * 3 Clause BSD Licence
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 13/03/16
 */
class NotFoundException extends RuntimeException implements NotFoundExceptionInterface
{
}
