<?php

namespace Fixin\Cargo;

class Cargo implements CargoInterface {

    /**
     * Content
     *
     * @var mixed
     */
    protected $content;

    /**
     * Gets content
     *
     * @return mixed
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * Sets content
     *
     * @param mixed $content
     * @return self
     */
    public function setContent($content) {
        $this->content = $content;

        return $this;
    }
}