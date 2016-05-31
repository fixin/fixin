<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Cargo;

use Fixin\Resource\Prototype;

class Cargo extends Prototype implements CargoInterface {

    /**
     * @var mixed
     */
    protected $content;

    /**
     * @var string
     */
    protected $contentType = '';

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
     * @see \Fixin\Delivery\Cargo\CargoInterface::getContentType()
     */
    public function getContentType(): string {
        return $this->contentType;
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
     * @see \Fixin\Delivery\Cargo\CargoInterface::setContent($content)
     */
    public function setContent($content): CargoInterface {
        $this->content = $content;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\CargoInterface::setContentType($contentType)
     */
    public function setContentType(string $contentType): CargoInterface {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\CargoInterface::setDelivered($delivered)
     */
    public function setDelivered(bool $delivered): CargoInterface {
        $this->delivered = $delivered;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\CargoInterface::unpack()
     */
    public function unpack() {
        echo $this->content;
    }
}