<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Cargo;

interface CargoInterface {

    /**
     * Get content
     *
     * @return mixed
     */
    public function getContent();

    /**
     * Set content
     *
     * @param mixed $content
     * @return self
     */
    public function setContent($content);
}