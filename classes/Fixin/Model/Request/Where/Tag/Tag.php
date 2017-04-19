<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Request\Where\Tag;

use Fixin\Resource\Prototype;
use Fixin\Support\Types;

abstract class Tag extends Prototype implements TagInterface
{
    protected const
        THIS_SETS = [
            self::JOIN => Types::STRING,
            self::NEGATED => Types::STRING
        ];

    /**
     * @var string
     */
    protected $join = self::JOIN_AND;

    /**
     * @var bool
     */
    protected $negated = false;

    public function getJoin(): string
    {
        return $this->join;
    }

    public function isNegated(): bool
    {
        return $this->negated;
    }
}
