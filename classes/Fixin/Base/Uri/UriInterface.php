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
    public const
        OPTION_FRAGMENT = 'fragment',
        OPTION_HOST = 'host',
        OPTION_PATH = 'path',
        OPTION_PORT = 'port',
        OPTION_QUERY = 'query',
        OPTION_SCHEME = 'scheme',
        OPTION_USER_INFO = 'userInfo';

    public function getAuthority(): string;
    public function getFragment(): ?string;
    public function getHost(): string;
    public function getPath(): string;
    public function getPort(): ?int;
    public function getQuery(): string;
    public function getScheme(): string;
    public function getUserInfo(): string;
}
