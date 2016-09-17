<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Query;

use Fixin\Resource\PrototypeInterface;

interface QueryInterface extends PrototypeInterface {

    /**
     * Add parameter
     *
     * @param mixed $parameter
     * @return self
     */
    public function addParameter($parameter): QueryInterface;

    /**
     * Add parameters
     *
     * @param array $parameters
     * @return self
     */
    public function addParameters(array $parameters): QueryInterface;

    /**
     * Append clause
     *
     * @param string $clause
     * @param string $string
     * @return self
     */
    public function appendClause(string $clause, string $string): QueryInterface;

    /**
     * Append string
     *
     * @param string $string
     * @return self
     */
    public function appendString(string $string): QueryInterface;

    /**
     * Append word
     *
     * @param string $word
     * @return self
     */
    public function appendWord(string $word): QueryInterface;

    /**
     * Apply mask
     *
     * @param string $mask
     * @return self
     */
    public function applyMask(string $mask): QueryInterface;

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