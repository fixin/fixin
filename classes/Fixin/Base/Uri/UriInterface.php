<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Uri;

interface UriInterface {

    /**
     * Get authority part
     *
     * @return string
     */
    public function getAuthority(): string;

    /**
     * Get fragment
     *
     * @return string|null
     */
    public function getFragment();

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
    public function getPort();

    /**
     * Get query string
     *
     * @return string
     */
    public function getQuery(): string;

    /**
     * Get scheme
     *
     * @return string
     */
    public function getScheme(): string;

    /**
     * Get user info
     *
     * @return string
     */
    public function getUserInfo(): string;

    /**
     * Set fragment
     *
     * @param string|null $fragment
     * @return self
     */
    public function setFragment($fragment);

    /**
     * Set host
     *
     * @param string $host
     * @return self
     */
    public function setHost(string $host);

    /**
     * Set path
     *
     * @param string $path
     * @return self
     */
    public function setPath(string $path);

    /**
     * Set port
     *
     * @param int|null $port
     * @return self
     */
    public function setPort($port);

    /**
     * Set query
     *
     * @param string $query
     * @return self
     */
    public function setQuery(string $query);

    /**
     * Set scheme
     *
     * @param string $scheme
     * @return self
     */
    public function setScheme(string $scheme);

    /**
     * Set user info
     *
     * @param string $userInfo
     * @return self
     */
    public function setUserInfo(string $userInfo);
}