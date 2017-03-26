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

class WhereTag extends Tag
{
    protected const
        THIS_REQUIRES = [
            self::WHERE
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

    protected function setWhere(WhereInterface $where): void
    {
        $this->where = $where;
    }
}
