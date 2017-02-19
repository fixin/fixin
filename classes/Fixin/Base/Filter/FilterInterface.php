<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Filter;

use Fixin\Resource\ResourceInterface;

interface FilterInterface extends ResourceInterface
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
