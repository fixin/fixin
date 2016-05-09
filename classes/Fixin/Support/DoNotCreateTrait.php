<?php

namespace Fixin\Support;

trait DoNotCreateTrait {

    /**
     * Block creation
     *
     * @throws Exception\DoNotCreateException
     */
	final public function __construct() {
        throw new Exception\DoNotCreateException("Don't create instance for " . __CLASS__);
    }
}