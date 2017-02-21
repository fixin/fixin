<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request;

use Fixin\Resource\PrototypeInterface;

interface ExpressionInterface extends PrototypeInterface
{
    public const
        OPTION_EXPRESSION = 'expression',
        OPTION_PARAMETERS = 'parameters';

    public function getExpression(): string;
    public function getParameters(): array;
}
