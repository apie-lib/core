<?php
namespace Apie\Core\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Base class that is extended by all Apie exception classes.
 */
abstract class ApieException extends HttpException
{
}
