<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Repository\Where;

use Fixin\Resource\PrototypeInterface;

interface WhereInterface extends PrototypeInterface {

    const JOIN_AND = 'and';
    const JOIN_OR = 'or';
    const OPTION_JOIN = 'join';
    const OPTION_NEGATED = 'negated';

    /**
     * Get join
     *
     * @return string
     */
    public function getJoin(): string;

    /**
     * Is negated
     *
     * @return bool
     */
    public function isNegated(): bool;
}