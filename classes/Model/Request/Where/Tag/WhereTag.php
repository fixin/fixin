<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Request\Where\Tag;

use Fixin\Model\Request\Where\WhereInterface;

class WhereTag extends AbstractTag
{
    public const
        WHERE = 'where';

    protected const
        THIS_SETS = parent::THIS_SETS + [
            self::WHERE => WhereInterface::class
        ];

    /**
     * @var WhereInterface
     */
    protected $where;

    /**
     * Get where
     *
     * @return WhereInterface
     */
    public function getWhere(): WhereInterface
    {
        return $this->where;
    }
}
