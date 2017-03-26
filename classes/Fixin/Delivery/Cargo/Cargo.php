<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Delivery\Cargo;

use Fixin\Resource\Prototype;

class Cargo extends Prototype implements CargoInterface
{
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

    public function getContent()
    {
        return $this->content;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function isDelivered(): bool
    {
        return $this->delivered;
    }

    /**
     * @return $this
     */
    public function setContent($content): CargoInterface
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return $this
     */
    public function setContentType(string $contentType): CargoInterface
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @return $this
     */
    public function setDelivered(bool $delivered): CargoInterface
    {
        $this->delivered = $delivered;

        return $this;
    }

    /**
     * @return $this
     */
    public function unpack(): CargoInterface
    {
        echo $this->content;

        return $this;
    }
}
