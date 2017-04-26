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
use Fixin\Support\DebugDescriptionTrait;
use Fixin\Support\Types;

abstract class Tag extends Prototype implements TagInterface
{
    use DebugDescriptionTrait;

    protected const
        THIS_SETS = [
            self::JOIN => Types::STRING,
            self::POSITIVE => Types::BOOL
        ];

    /**
     * @var string
     */
    protected $join = self::JOIN_AND;

    /**
     * @var bool
     */
    protected $positive = true;

    public function getJoin(): string
    {
        return $this->join;
    }

    public function isPositive(): bool
    {
        return $this->positive;
    }
}
