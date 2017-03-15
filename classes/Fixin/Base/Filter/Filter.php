<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
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
