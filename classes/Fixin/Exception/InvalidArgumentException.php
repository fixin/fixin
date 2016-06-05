<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Exception;

class InvalidArgumentException extends \InvalidArgumentException implements FixinException {

    const INVALID_ARGUMENT = "Invalid '%s' argument: %s allowed";
    const INVALID_RESOURCE = "Invalid '%s' resource: %s allowed";
}