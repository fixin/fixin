<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Request;

use Fixin\Resource\PrototypeInterface;

interface ExpressionInterface extends PrototypeInterface
{
    public const
        EXPRESSION = 'expression',
        PARAMETERS = 'parameters';

    public function getExpression(): string;
    public function getParameters(): array;
}
