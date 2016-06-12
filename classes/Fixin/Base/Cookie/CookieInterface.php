<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Cookie;

use Fixin\Resource\PrototypeInterface;

interface CookieInterface extends PrototypeInterface {

    /**
     * Get domain
     *
     * @return string
     */
    public function getDomain(): string;

    /**
     * Get expire in minutes
     *
     * @return int
     */
    public function getExpire(): int;

    /**
     * Get path
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * Get value
     *
     * @return string
     */
    public function getValue(): string;

    /**
     * Determine if HTTP-only
     *
     * @return bool
     */
    public function isHttpOnly(): bool;

    /**
     * Determine if secure
     *
     * @return bool
     */
    public function isSecure(): bool;

    /**
     * Send as
     *
     * @param string $name
     * @return self
     */
    public function sendAs(string $name): self;

    /**
     * Set domain
     *
     * @param string $domain
     * @return self
     */
    public function setDomain(string $domain): self;

    /**
     * Set expire in minutes
     *
     * @param int $expire
     * @return self
     */
    public function setExpire(int $expire): self;

    /**
     * Set HTTP only
     *
     * @param bool $httpOnly
     * @return self
     */
    public function setHttpOnly(bool $httpOnly): self;

    /**
     * Set path
     *
     * @param string $path
     * @return self
     */
    public function setPath(string $path): self;

    /**
     * Set secure
     *
     * @param bool $secure
     * @return self
     */
    public function setSecure(bool $secure): self;

    /**
     * Set value
     *
     * @param string $value
     * @return self
     */
    public function setValue(string $value): self;
}