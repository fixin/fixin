<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request;

use Fixin\Resource\PrototypeInterface;

interface UnionInterface extends PrototypeInterface
{
    public const
        OPTION_REQUEST = 'request',
        OPTION_TYPE = 'type',
        TYPE_ALL = 'all',
        TYPE_NORMAL = 'normal';

    public function getRequest(): RequestInterface;
    public function getType(): string;
}
