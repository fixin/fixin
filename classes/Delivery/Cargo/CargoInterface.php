<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Delivery\Cargo;

use Fixin\Resource\PrototypeInterface;

interface CargoInterface extends PrototypeInterface
{
    public const
        CONTENT = 'content',
        CONTENT_TYPE = 'contentType',
        DELIVERED = 'delivered';

    /**
     * Get content
     *
     * @return mixed
     */
    public function getContent();

    /**
     * Get content type
     *
     * @return string
     */
    public function getContentType(): string;

    /**
     * Determine if is delivered
     *
     * @return bool
     */
    public function isDelivered(): bool;

    /**
     * Set content
     *
     * @param $content
     * @return $this
     */
    public function setContent($content): CargoInterface;

    /**
     * Set content type
     *
     * @param string $contentType
     * @return $this
     */
    public function setContentType(string $contentType): CargoInterface;

    /**
     * Set delivered
     *
     * @param bool $delivered
     * @return $this
     */
    public function setDelivered(bool $delivered): CargoInterface;

    /**
     * Unpack
     *
     * @return $this
     */
    public function unpack(): CargoInterface;
}
