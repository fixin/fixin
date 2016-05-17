<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Cargo;

use Fixin\Support\PrototypeInterface;

interface CargoInterface extends PrototypeInterface {

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
     * Is delivered
     *
     * @return bool
     */
    public function isDelivered(): bool;

    /**
     * Set content
     *
     * @param mixed $content
     * @return self
     */
    public function setContent($content);

    /**
     * Set content type
     *
     * @param string $contentType
     * @return self
     */
    public function setContentType(string $contentType);

    /**
     * Set delivered state
     *
     * @param bool $delivered
     * @return self
     */
    public function setDelivered(bool $delivered);

    /**
     * Unpack cargo
     */
    public function unpack();
}