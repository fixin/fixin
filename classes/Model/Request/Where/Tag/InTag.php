<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Request\Where\Tag;

use Fixin\Model\Request\RequestInterface;
use Fixin\Support\Types;

class InTag extends AbstractIdentifierTag
{
    protected const
        THIS_SETS = parent::THIS_SETS + [
            self::VALUES => [Types::ARRAY, RequestInterface::class]
        ];

    public const
        VALUES = 'values';

    /**
     * @var array|RequestInterface
     */
    protected $values;

    /**
     * @return array|RequestInterface
     */
    public function getValues()
    {
        return $this->values;
    }
}
