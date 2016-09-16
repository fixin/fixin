<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request\Where\Tag;

use Fixin\Model\Request\RequestInterface;

class InTag extends IdentifierTag {

    const OPTION_VALUES = 'values';
    const THIS_REQUIRES = [
        self::OPTION_IDENTIFIER => self::TYPE_ANY,
        self::OPTION_VALUES => self::TYPE_ANY,
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
    public function getValues() {
        return $this->values;
    }

    /**
     * Set values
     *
     * @param array|RequestInterface $values
     */
    protected function setValues($values) {
        $this->values = $values;
    }
}