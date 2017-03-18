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

abstract class Tag extends Prototype implements TagInterface
{
    protected const
        THIS_REQUIRES = [
            self::OPTION_JOIN
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

    protected function setJoin(string $join): void
    {
        $this->join = $join;
    }

    protected function setNegated(bool $negated): void
    {
        $this->negated = $negated;
    }
}
