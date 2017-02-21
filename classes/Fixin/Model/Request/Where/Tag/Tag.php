<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request\Where\Tag;

use Fixin\Resource\Prototype;

abstract class Tag extends Prototype implements TagInterface
{
    protected const
        THIS_REQUIRES = [
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

    public function getJoin(): string
    {
        return $this->join;
    }

    public function isNegated(): bool
    {
        return $this->negated;
    }

    protected function setJoin(string $join)
    {
        $this->join = $join;
    }

    protected function setNegated(bool $negated)
    {
        $this->negated = $negated;
    }
}
