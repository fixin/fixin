<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request\Where\Tag;

use Fixin\Model\Request\RequestInterface;
use Fixin\Resource\Prototype;

abstract class Tag extends Prototype implements TagInterface {

    const THIS_REQUIRES = [
        self::OPTION_JOIN => self::TYPE_STRING
    ];

    /**
     * @var string
     */
    protected $join = self::JOIN_AND;

    /**
     * @var bool
     */
    protected $negated = false;

    /**
     * Closure to request process
     *
     * @param \Closure $closure
     * @return RequestInterface
     */
    protected function closureToRequest(\Closure $closure): RequestInterface {
        $request = new static();
        $closure($request);

        return $request;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\Tag\TagInterface::getJoin()
     */
    public function getJoin(): string {
        return $this->join;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\Where\Tag\TagInterface::isNegated()
     */
    public function isNegated(): bool {
        return $this->negated;
    }

    /**
     * Set join
     *
     * @param string $join
     */
    protected function setJoin(string $join) {
        $this->join = $join;
    }

    /**
     * Set negated
     *
     * @param bool $negated
     */
    protected function setNegated(bool $negated) {
        $this->negated = $negated;
    }
}