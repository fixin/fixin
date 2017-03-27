<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Request\Where\Tag;

abstract class IdentifierTag extends Tag
{
    protected const
        THIS_REQUIRES = [
            self::IDENTIFIER
        ],
        THIS_SETS = [
            self::IDENTIFIER => [self::STRING_TYPE, self::ARRAY_TYPE]
        ];

    public const
        IDENTIFIER = 'identifier';

    /**
     * @var string|array
     */
    protected $identifier;

    /**
     * @return string|array
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}
