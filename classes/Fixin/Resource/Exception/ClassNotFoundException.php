<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Resource\Exception;

use Fixin\Exception\InvalidArgumentException;

class ClassNotFoundException extends InvalidArgumentException implements ResourceException {
}