<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request;

use Fixin\Resource\Prototype;

class Union extends Prototype implements UnionInterface {

    const THIS_REQUIRES = [
        self::OPTION_REQUEST => self::TYPE_INSTANCE,
        self::OPTION_TYPE => self::TYPE_STRING
    ];

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var string
     */
    protected $type;

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\UnionInterface::getRequest()
     */
    public function getRequest(): RequestInterface {
        return $this->request;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\UnionInterface::getType()
     */
    public function getType(): string {
        return $this->type;
    }

    /**
     * Set request
     *
     * @param RequestInterface $request
     */
    protected function setRequest(RequestInterface $request) {
        $this->request = $request;
    }

    /**
     * Set type
     *
     * @param string $type
     */
    protected function setType(string $type) {
        $this->type = $type;
    }
}