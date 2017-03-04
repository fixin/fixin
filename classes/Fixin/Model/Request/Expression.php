<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request;

use Fixin\Resource\Prototype;

class Expression extends Prototype implements ExpressionInterface
{
    protected const
        THIS_REQUIRES = [
            self::OPTION_EXPRESSION => self::TYPE_STRING,
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

    protected function setExpression(string $expression): void
    {
        $this->expression = $expression;
    }

    protected function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }
}
