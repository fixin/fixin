<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Storage\Pdo;

use Fixin\Resource\Prototype;

class Query extends Prototype implements QueryInterface {

    protected $paramters = [];
    protected $text = '';

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Pdo\QueryInterface::addParameter($value)
     */
    public function addParameter($value): QueryInterface {
        $this->paramters[] = $value;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Pdo\QueryInterface::appendText($string)
     */
    public function appendText(string $string): QueryInterface {
        $this->text .= $string . PHP_EOL;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Pdo\QueryInterface::getParameters()
     */
    public function getParameters(): array {
        return $this->paramters;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\Pdo\QueryInterface::getText()
     */
    public function getText(): string {
        return $this->text;
    }
}