<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Storage\Pdo;

use Fixin\Resource\PrototypeInterface;

interface QueryInterface extends PrototypeInterface {

    /**
     * Add parameter
     *
     * @param mixed $value
     * @return self
     */
    public function addParameter($value): self;

    /**
     * Append clause
     *
     * @param string $clause
     * @param string $string
     * @return self
     */
    public function appendClause(string $clause, string $string): self;

    /**
     * Append text
     *
     * @param string $string
     * @return self
     */
    public function appendText(string $string): self;

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParameters(): array;

    /**
     * Get text
     *
     * @return string
     */
    public function getText(): string;
}