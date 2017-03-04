<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request\Where\Tag;

use Fixin\Model\Request\RequestInterface;

class InTag extends IdentifierTag
{
    protected const
        THIS_REQUIRES = [
            self::OPTION_IDENTIFIER => self::TYPE_ANY,
            self::OPTION_VALUES => self::TYPE_ANY,
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
