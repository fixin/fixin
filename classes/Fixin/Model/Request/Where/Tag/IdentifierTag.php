<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request\Where\Tag;

use Fixin\Model\Request\RequestInterface;

abstract class IdentifierTag extends Tag {

    const OPTION_IDENTIFIER = 'identifier';
    const THIS_REQUIRES = [
        self::OPTION_IDENTIFIER => self::TYPE_ANY,
    ];

    /**
     * @var string|RequestInterface|\Closure
     */
    protected $identifier;

    /**
     * Get identifier
     *
     * @return string|RequestInterface|\Closure
     */
    public function getIdentifier() {
        return $this->identifier;
    }

    /**
     * Set identifier
     *
     * @param string $identifier
     */
    protected function setIdentifier(string $identifier) {
        $this->identifier = $identifier;
    }
}