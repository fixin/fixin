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
            self::OPTION_IDENTIFIER
        ];

    public const
        OPTION_IDENTIFIER = 'identifier';

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

    /**
     * @param string|array $identifier
     */
    protected function setIdentifier($identifier): void
    {
        $this->identifier = $identifier;
    }
}
