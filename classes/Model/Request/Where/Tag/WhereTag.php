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
    protected const
        THIS_SETS = parent::THIS_SETS + [
            self::WHERE => WhereInterface::class
        ];

    public const
        WHERE = 'where';

    /**
     * @var WhereInterface
     */
    protected $where;

    public function getWhere(): WhereInterface
    {
        return $this->where;
    }
}
