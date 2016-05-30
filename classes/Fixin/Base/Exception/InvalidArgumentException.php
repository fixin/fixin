<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Exception;

class InvalidArgumentException extends \BadMethodCallException {

    const MESSAGE = "Invalid '%s' argument: %s allowed";
}