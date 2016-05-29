<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Filter;

use Fixin\Resource\ResourceInterface;

interface FilterInterface extends ResourceInterface {

    /**
     * Get filtered value
     *
     * @param mixed $value
     * @return mixed
     */
    public function filter($value);
}