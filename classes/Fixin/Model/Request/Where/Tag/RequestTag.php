<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request\Where\Tag;

use Fixin\Model\Request\RequestInterface;

class RequestTag extends Tag {

    const OPTION_REQUEST = 'request';
    const THIS_REQUIRES = [
        self::OPTION_REQUEST => self::TYPE_INSTANCE,
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
    public function getRequest(): RequestInterface {
        return $this->request;
    }

    /**
     * Set request
     *
     * @param RequestInterface $request
     */
    protected function setRequest(RequestInterface $request) {
        $this->request = $request;
    }
}