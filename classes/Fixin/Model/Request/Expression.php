<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Request;

use Fixin\Resource\Prototype;

class Expression extends Prototype implements ExpressionInterface
{
    protected const
        THIS_REQUIRES = [
            self::EXPRESSION
        ],
        THIS_SETS = [
            self::EXPRESSION => self::STRING_TYPE,
            self::PARAMETERS => self::ARRAY_TYPE
        ];

    /**
     * @var string
     */
    protected $expression;

    /**
     * @var array
     */
    protected $parameters = [];

    public function getExpression(): string
    {
        return $this->expression;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
