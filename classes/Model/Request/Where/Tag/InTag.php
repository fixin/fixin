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
    public const
        VALUES = 'values';

    protected const
        THIS_SETS = parent::THIS_SETS + [
            self::VALUES => [Types::ARRAY, RequestInterface::class]
        ];

    /**
     * @var array|RequestInterface
     */
    protected $values;

    /**
     * Get values
     *
     * @return array|RequestInterface
     */
    public function getValues()
    {
        return $this->values;
    }
}
