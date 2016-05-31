<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Support;

use Fixin\Support\Exception\DoNotCreateException;

abstract class DoNotCreate {

    const EXCEPTION_DO_NOT_CREATE = "Do not create instance of %s";

    /**
     * Block creation
     *
     * @throws DoNotCreateException
     */
    final public function __construct() {
        throw new DoNotCreateException(sprintf(static::EXCEPTION_DO_NOT_CREATE, __CLASS__));
    }
}