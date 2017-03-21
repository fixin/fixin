<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Sentence;

use Fixin\Resource\Prototype;
use Fixin\Support\Ground;
use Fixin\Support\VariableInspector;

class Sentence extends Prototype implements SentenceInterface
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
        return Ground::toDebugText($this->text) . Ground::toDebugText(VariableInspector::arrayInfo($this->parameters));
    }

    /**
     * @return static
     */
    public function addParameter($parameter): SentenceInterface
    {
        $this->parameters[] = $parameter;

        return $this;
    }

    /**
     * @return static
     */
    public function addParameters(array $parameters): SentenceInterface
    {
        $this->parameters = array_merge($this->parameters, $parameters);

        return $this;
    }

    /**
     * @return static
     */
    public function appendClause(string $clause, string $string): SentenceInterface
    {
        $this->text .= $clause . ' ' . $string . PHP_EOL;

        return $this;
    }

    /**
     * @return static
     */
    public function appendString(string $string): SentenceInterface
    {
        $this->text .= $string;

        return $this;
    }

    /**
     * @return static
     */
    public function appendWord(string $word): SentenceInterface
    {
        $this->text .= $word . ' ';

        return $this;
    }

    /**
     * @return static
     */
    public function applyMask(string $mask): SentenceInterface
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
