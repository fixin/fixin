<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request;

use Fixin\Resource\Prototype;

class Expression extends Prototype implements ExpressionInterface {

    const THIS_REQUIRES = [
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

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\ExpressionInterface::getExpression()
     */
    public function getExpression(): string {
        return $this->expression;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Request\ExpressionInterface::getParameters()
     */
    public function getParameters(): array {
        return $this->parameters;
    }

    /**
     * Set expression
     *
     * @param string $expression
     */
    protected function setExpression(string $expression) {
        $this->expression = $expression;
    }

    /**
     * Set parameters
     *
     * @param array $parameters
     */
    protected function setParameters(array $parameters) {
        $this->parameters = $parameters;
    }
}