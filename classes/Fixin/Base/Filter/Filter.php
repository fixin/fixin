<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Filter;

use Fixin\Resource\Prototype;

abstract class Filter extends Prototype implements FilterInterface
{
    public function __invoke($value)
    {
        return $this->filter($value);
    }
}
