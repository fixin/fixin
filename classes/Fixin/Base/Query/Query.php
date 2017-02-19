<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Query;

use Fixin\Resource\Prototype;
use Fixin\Support\Ground;
use Fixin\Support\VariableInspector;

class Query extends Prototype implements QueryInterface
{
    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var string
     */
    protected $text = '';

    public function __toString(): string
    {
        return Ground::debugText($this->text) . Ground::debugText(VariableInspector::arrayInfo($this->parameters));
    }

    /**
     * @return static
     */
    public function addParameter($parameter): QueryInterface
    {
        $this->parameters[] = $parameter;

        return $this;
    }

    /**
     * @return static
     */
    public function addParameters(array $parameters): QueryInterface
    {
        $this->parameters = array_merge($this->parameters, $parameters);

        return $this;
    }

    /**
     * @return static
     */
    public function appendClause(string $clause, string $string): QueryInterface
    {
        $this->text .= $clause . ' ' . $string . PHP_EOL;

        return $this;
    }

    /**
     * @return static
     */
    public function appendString(string $string): QueryInterface
    {
        $this->text .= $string;

        return $this;
    }

    /**
     * @return static
     */
    public function appendWord(string $word): QueryInterface
    {
        $this->text .= $word . ' ';

        return $this;
    }

    /**
     * @return static
     */
    public function applyMask(string $mask): QueryInterface
    {
        $this->text = sprintf($mask, $this->text);

        return $this;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
