<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Filter;

use Fixin\Resource\Resource;

abstract class Filter extends Resource implements FilterInterface
{
    public function __invoke($value)
    {
        return $this->filter($value);
    }
}
