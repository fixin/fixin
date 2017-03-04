<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request\Where\Tag;

abstract class IdentifierTag extends Tag
{
    protected const
        THIS_REQUIRES = [
            self::OPTION_IDENTIFIER => self::TYPE_ANY,
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
