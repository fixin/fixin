<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Request\Where\Tag;

use Fixin\Support\Types;

abstract class AbstractIdentifierTag extends AbstractTag
{
    public const
        IDENTIFIER = 'identifier';

    protected const
        THIS_SETS = parent::THIS_SETS + [
            self::IDENTIFIER => [Types::STRING, Types::ARRAY]
        ];

    /**
     * @var string|array
     */
    protected $identifier;

    /**
     * Get identifier
     *
     * @return string|array
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}
