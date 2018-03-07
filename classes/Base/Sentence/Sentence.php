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

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return Ground::toDebugBlock($this->text) . Ground::toDebugText(VariableInspector::arrayInfo($this->parameters));
    }

    /**
     * @inheritDoc
     */
    public function addParameter($parameter): SentenceInterface
    {
        $this->parameters[] = $parameter;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addParameters(array $parameters): SentenceInterface
    {
        $this->parameters = array_merge($this->parameters, $parameters);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function appendClause(string $clause, string $string): SentenceInterface
    {
        $this->text .= $clause . ' ' . $string . PHP_EOL;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function appendString(string $string): SentenceInterface
    {
        $this->text .= $string;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function appendWord(string $word): SentenceInterface
    {
        $this->text .= $word . ' ';

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function applyMask(string $mask): SentenceInterface
    {
        $this->text = sprintf($mask, $this->text);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return $this->text;
    }
}
