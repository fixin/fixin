<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Container;

use Fixin\Resource\Resource;
use Fixin\Support\Types;

class Container extends Resource implements ContainerInterface
{
    use ContainerTrait;

    protected const
        THIS_SETS = [
            self::VALUES => Types::ARRAY
        ];
}
