<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request;

use Fixin\Resource\PrototypeInterface;

interface UnionInterface extends PrototypeInterface {

    const OPTION_REQUEST = 'request';
    const OPTION_TYPE = 'type';
    const TYPE_ALL = 'all';
    const TYPE_NORMAL = 'normal';

    /**
     * Get request
     *
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface;

    /**
     * Get type
     *
     * @return string
     */
    public function getType(): string;
}