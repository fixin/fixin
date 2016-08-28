<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request\Where\Tag;

use Fixin\Model\Request\Where\WhereInterface;

class WhereTag extends Tag {

    const OPTION_WHERE = 'where';
    const THIS_REQUIRES = [
        self::OPTION_WHERE => self::TYPE_INSTANCE,
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
    public function getWhere(): WhereInterface {
        return $this->where;
    }

    /**
     * Set where
     *
     * @param WhereInterface $where
     */
    protected function setWhere(WhereInterface $where) {
        $this->where = $where;
    }
}