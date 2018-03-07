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
     *
     * @param $value
     * @return mixed
     */
    public function __invoke($value);

    /**
     * Get filtered value
     *
     * @param $value
     * @return mixed
     */
    public function filter($value);
}
