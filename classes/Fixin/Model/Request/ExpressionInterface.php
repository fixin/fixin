<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request;

use Fixin\Resource\PrototypeInterface;

interface ExpressionInterface extends PrototypeInterface {

    const OPTION_EXPRESSION = 'expression';
    const OPTION_PARAMETERS = 'parameters';

    /**
     * Get expression
     *
     * @return string
     */
    public function getExpression(): string;

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParameters(): array;
}