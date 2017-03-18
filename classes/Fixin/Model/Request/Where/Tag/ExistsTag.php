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

class ExistsTag extends Tag
{
    protected const
        THIS_REQUIRES = [
            self::OPTION_REQUEST
        ];

    public const
        OPTION_REQUEST = 'request';

    /**
     * @var RequestInterface
     */
    protected $request;

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    protected function setRequest(RequestInterface $request): void
    {
        $this->request = $request;
    }
}
