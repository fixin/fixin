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

class InTag extends IdentifierTag
{
    protected const
        THIS_REQUIRES = [
            self::OPTION_IDENTIFIER,
            self::OPTION_VALUES
        ];

    public const
        OPTION_VALUES = 'values';

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

    /**
     * @param array|RequestInterface $values
     */
    protected function setValues($values): void
    {
        $this->values = $values;
    }
}
