<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request\Where\Tag;

abstract class IdentifierTag extends Tag {

    const OPTION_IDENTIFIER = 'identifier';
    const THIS_REQUIRES = [
        self::OPTION_IDENTIFIER => self::TYPE_ANY,
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
    public function getIdentifier() {
        return $this->identifier;
    }

    /**
     * Set identifier
     *
     * @param string|array $identifier
     */
    protected function setIdentifier($identifier) {
        $this->identifier = $identifier;
    }
}