<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Request\Where\Tag;

use Fixin\Model\Request\RequestInterface;

class ExistsTag extends AbstractTag
{
    public const
        REQUEST = 'request';

    protected const
        THIS_SETS = parent::THIS_SETS + [
            self::REQUEST => RequestInterface::class
        ];

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * Get request
     *
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
