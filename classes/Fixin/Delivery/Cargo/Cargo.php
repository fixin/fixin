<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Cargo;

class Cargo implements CargoInterface {

    /**
     * @var mixed
     */
    protected $content;

    /**
     * @var bool
     */
    protected $delivered = false;

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\CargoInterface::getContent()
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\CargoInterface::isDelivered()
     */
    public function isDelivered(): bool {
        return $this->delivered;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\CargoInterface::setContent()
     */
    public function setContent($content) {
        $this->content = $content;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\CargoInterface::setDelivered($delivered)
     */
    public function setDelivered(bool $delivered) {
        $this->delivered = $delivered;

        return $this;
    }
}