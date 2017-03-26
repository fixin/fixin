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

    public function getContent();
    public function getContentType(): string;
    public function isDelivered(): bool;

    /**
     * @return $this
     */
    public function setContent($content): CargoInterface;

    /**
     * @return $this
     */
    public function setContentType(string $contentType): CargoInterface;

    /**
     * @return $this
     */
    public function setDelivered(bool $delivered): CargoInterface;

    /**
     * @return $this
     */
    public function unpack(): CargoInterface;
}
