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
use Fixin\Support\Types;

class Expression extends Prototype implements ExpressionInterface
{
    protected const
        THIS_SETS = [
            self::EXPRESSION => Types::STRING,
            self::PARAMETERS => Types::ARRAY
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
