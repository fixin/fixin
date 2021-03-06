<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Request\Where\Tag;

use Fixin\Resource\PrototypeInterface;

interface TagInterface extends PrototypeInterface
{
    public const
        JOIN = 'join',
        JOIN_AND = 'and',
        JOIN_OR = 'or',
        POSITIVE = 'positive';

    public function getJoin(): string;
    public function isPositive(): bool;
}
