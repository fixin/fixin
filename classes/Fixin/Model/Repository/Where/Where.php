<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Repository\Where;

use Fixin\Resource\Prototype;

abstract class Where extends Prototype implements WhereInterface {

    const THIS_REQUIRES = [
        self::OPTION_JOIN => self::TYPE_STRING
    ];

    /**
     * @var string
     */
    protected $join = static::JOIN_AND;

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\Where\WhereInterface::getJoin()
     */
    public function getJoin(): string {
        return $this->join;
    }

    /**
     * Set join
     *
     * @param string $join
     */
    protected function setJoin(string $join) {
        $this->join = $join;
    }
}