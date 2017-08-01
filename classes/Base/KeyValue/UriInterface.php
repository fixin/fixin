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

    public function getAuthority(): ?string;
    public function getFragment(): ?string;
    public function getHost(): string;
    public function getPath(): string;
    public function getPort(): ?int;
    public function getQuery(): string;
    public function getScheme(): ?string;
    public function getUserInfo(): ?string;
}
