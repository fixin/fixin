<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Uri;

use Fixin\Resource\PrototypeInterface;

interface UriInterface extends PrototypeInterface
{
    // TODO OPTION_* ?

    public function getAuthority(): string;
    public function getFragment(): ?string;
    public function getHost(): string;
    public function getPath(): string;
    public function getPort(): ?int;
    public function getQuery(): string;
    public function getScheme(): string;
    public function getUserInfo(): string;
    public function setFragment(?string $fragment): UriInterface;
    public function setHost(string $host): UriInterface;
    public function setPath(string $path): UriInterface;
    public function setPort(?int $port): UriInterface;
    public function setQuery(string $query): UriInterface;
    public function setScheme(string $scheme): UriInterface;
    public function setUserInfo(string $userInfo): UriInterface;
}
