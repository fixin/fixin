<?php

namespace Fixin\Support;

abstract class DoNotCreate {

    /**
     * Block creation
     *
     * @throws Exception\DoNotCreateException
     */
    final public function __construct() {
        throw new Exception\DoNotCreateException("Don't create instance for " . __CLASS__);
    }
}