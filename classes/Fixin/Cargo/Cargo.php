<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Cargo;

class Cargo implements CargoInterface {

    /**
     * @var mixed
     */
    protected $content;

    /**
     * {@inheritDoc}
     * @see \Fixin\Cargo\CargoInterface::getContent()
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Cargo\CargoInterface::setContent()
     */
    public function setContent($content) {
        $this->content = $content;

        return $this;
    }
}