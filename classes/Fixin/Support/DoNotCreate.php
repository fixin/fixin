<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Support;

abstract class DoNotCreate
{
    protected const
        EXCEPTION_DO_NOT_CREATE = "Do not create instance of %s";

    /**
     * Block creation
     *
     * @throws Exception\DoNotCreateException
     */
    final public function __construct()
    {
        throw new Exception\DoNotCreateException(sprintf(static::EXCEPTION_DO_NOT_CREATE, __CLASS__));
    }
}
