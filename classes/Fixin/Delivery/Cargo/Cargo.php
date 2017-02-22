<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
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
     * @return static
     */
    public function setContent($content): CargoInterface
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return static
     */
    public function setContentType(string $contentType): CargoInterface
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @return static
     */
    public function setDelivered(bool $delivered): CargoInterface
    {
        $this->delivered = $delivered;

        return $this;
    }

    /**
     * @return static
     */
    public function unpack(): CargoInterface
    {
        echo $this->content;

        return $this;
    }
}
