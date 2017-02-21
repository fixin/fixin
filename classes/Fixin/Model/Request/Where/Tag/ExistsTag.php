<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request\Where\Tag;

use Fixin\Model\Request\RequestInterface;

class ExistsTag extends Tag
{
    protected const
        THIS_REQUIRES = [
            self::OPTION_REQUEST => self::TYPE_INSTANCE,
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

    protected function setRequest(RequestInterface $request)
    {
        $this->request = $request;
    }
}
