<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Support;

abstract class DoNotCreate
{
    protected const
        DO_NOT_CREATE_EXCEPTION = "Do not create instance of %s";

    /**
     * Block creation
     *
     * @throws Exception\DoNotCreateException
     */
    final public function __construct()
    {
        throw new Exception\DoNotCreateException(sprintf(static::DO_NOT_CREATE_EXCEPTION, __CLASS__));
    }
}
