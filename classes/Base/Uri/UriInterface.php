<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Uri;

use Fixin\Resource\PrototypeInterface;

interface UriInterface extends PrototypeInterface
{
    public const
        FRAGMENT = 'fragment',
        HOST = 'host',
        PATH = 'path',
        PORT = 'port',
        QUERY = 'query',
        SCHEME = 'scheme',
        USER_INFO = 'userInfo';

    /**
     * Get authority
     *
     * @return null|string
     */
    public function getAuthority(): ?string;

    /**
     * Get fragment
     *
     * @return null|string
     */
    public function getFragment(): ?string;

    /**
     * Get host
     *
     * @return string
     */
    public function getHost(): string;

    /**
     * Get path
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * Get port
     *
     * @return int|null
     */
    public function getPort(): ?int;

    /**
     * Get query string
     *
     * @return string
     */
    public function getQuery(): string;

    /**
     * Get scheme
     *
     * @return null|string
     */
    public function getScheme(): ?string;

    /**
     * Get user info
     *
     * @return null|string
     */
    public function getUserInfo(): ?string;
}
