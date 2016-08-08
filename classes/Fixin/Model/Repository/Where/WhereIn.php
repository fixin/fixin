<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Repository\Where;

use Fixin\Model\Repository\RepositoryRequestInterface;

class WhereIn extends WhereIdentifier {

    const OPTION_VALUES = 'values';
    const THIS_REQUIRES = [
        self::OPTION_IDENTIFIER => self::TYPE_ANY,
        self::OPTION_VALUES => self::TYPE_ANY,
    ];

    /**
     * @var array|RepositoryRequestInterface
     */
    protected $values;

    /**
     * Get values
     *
     * @return \Fixin\Model\Repository\RepositoryRequestInterface
     */
    public function getValues() {
        return $this->values;
    }

    /**
     * Set values
     *
     * @param array|RepositoryRequestInterface|\Closure $values
     */
    protected function setValues($values) {
        $this->values = $values instanceof \Closure ? $this->closureToRequest($values) : $values;
    }
}