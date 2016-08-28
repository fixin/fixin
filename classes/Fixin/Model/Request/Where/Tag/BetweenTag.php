<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request\Where\Tag;

class BetweenTag extends IdentifierTag {

    const OPTION_MAX = 'max';
    const OPTION_MIN = 'min';
    const THIS_REQUIRES = [
        self::OPTION_IDENTIFIER => self::TYPE_ANY,
        self::OPTION_MAX => self::TYPE_ANY,
        self::OPTION_MIN => self::TYPE_ANY,
    ];

    /**
     * @var number|string
     */
    protected $max;

    /**
     * @var number|string
     */
    protected $min;

    /**
     * Get max value
     *
     * @return number|string
     */
    public function getMax() {
        return $this->max;
    }

    /**
     * Get min value
     *
     * @return number|string
     */
    public function getMin() {
        return $this->min;
    }

    /**
     * Set max value
     *
     * @param number|string $max
     */
    protected function setMax($max) {
        $this->max = $max;
    }

    /**
     * Set min value
     *
     * @param number|string $min
     */
    protected function setMin($min) {
        $this->min = $min;
    }
}