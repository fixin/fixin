<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Storage\Pdo;

use Fixin\Resource\Prototype;
use Fixin\Support\Ground;

class Query extends Prototype implements QueryInterface {

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var string
     */
    protected $text = '';

    public function __toString(): string {
        return Ground::debugText($this->text);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Pdo\QueryInterface::addParameter($value)
     */
    public function addParameter($value): QueryInterface {
        $this->parameters[] = $value;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Pdo\QueryInterface::appendClause($clause, $string)
     */
    public function appendClause(string $clause, string $string): QueryInterface {
        $this->text .= $clause . ' ' . $string . PHP_EOL;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Pdo\QueryInterface::appendString($string)
     */
    public function appendString(string $string): QueryInterface {
        $this->text .= $string;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Pdo\QueryInterface::appendWord($word)
     */
    public function appendWord(string $word): QueryInterface {
        $this->text .= $word . ' ';

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Pdo\QueryInterface::getParameters()
     */
    public function getParameters(): array {
        return $this->parameters;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Pdo\QueryInterface::getText()
     */
    public function getText(): string {
        return $this->text;
    }
}