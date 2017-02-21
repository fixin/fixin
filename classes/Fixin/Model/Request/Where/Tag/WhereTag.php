<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request\Where\Tag;

use Fixin\Model\Request\Where\WhereInterface;

class WhereTag extends Tag
{
    protected const
        THIS_REQUIRES = [
            self::OPTION_WHERE => self::TYPE_INSTANCE,
        ];

    public const
        OPTION_WHERE = 'where';

    /**
     * @var WhereInterface
     */
    protected $where;

    public function getWhere(): WhereInterface
    {
        return $this->where;
    }

    protected function setWhere(WhereInterface $where)
    {
        $this->where = $where;
    }
}
