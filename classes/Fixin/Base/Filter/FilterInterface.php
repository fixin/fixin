<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
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
