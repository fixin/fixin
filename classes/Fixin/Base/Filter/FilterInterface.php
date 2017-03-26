<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Filter;

use Fixin\Resource\PrototypeInterface;

interface FilterInterface extends PrototypeInterface
{
    /**
     * Invoke filter()
     */
    public function __invoke($value);

    /**
     * Get filtered value
     */
    public function filter($value);
}
