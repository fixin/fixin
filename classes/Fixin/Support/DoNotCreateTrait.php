<?php

namespace Fixin\Support;

trait DoNotCreateTrait {

    /**
     * Block creation
     *
     * @throws Exception\UnsupportedMethodCallException
     */
	final public function __construct() {
        throw new Exception\UnsupportedMethodCallException('Don\'t create instance for ' . __CLASS__);
    }
}