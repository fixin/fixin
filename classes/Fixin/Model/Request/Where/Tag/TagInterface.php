<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request\Where\Tag;

use Fixin\Resource\PrototypeInterface;

interface TagInterface extends PrototypeInterface
{
    public const
        JOIN_AND = 'and',
        JOIN_OR = 'or',
        OPTION_JOIN = 'join',
        OPTION_NEGATED = 'negated';

    public function getJoin(): string;
    public function isNegated(): bool;
}
