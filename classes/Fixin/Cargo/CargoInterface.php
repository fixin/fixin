<?php

namespace Fixin\Cargo;

interface CargoInterface {

    /**
     * Gets content
     *
     * @return mixed
     */
    public function getContent();

    /**
     * Sets content
     *
     * @param mixed $content
     * @return self
     */
    public function setContent($content);
}