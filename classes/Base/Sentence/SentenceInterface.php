<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Sentence;

use Fixin\Resource\PrototypeInterface;

interface SentenceInterface extends PrototypeInterface
{
    /**
     * Add parameter
     *
     * @param $parameter
     * @return $this
     */
    public function addParameter($parameter): SentenceInterface;

    /**
     * Add parameters
     *
     * @param array $parameters
     * @return $this
     */
    public function addParameters(array $parameters): SentenceInterface;

    /**
     * Append clause
     *
     * @param string $clause
     * @param string $string
     * @return $this
     */
    public function appendClause(string $clause, string $string): SentenceInterface;

    /**
     * Append string
     *
     * @param string $string
     * @return $this
     */
    public function appendString(string $string): SentenceInterface;

    /**
     * Append word
     *
     * @param string $word
     * @return $this
     */
    public function appendWord(string $word): SentenceInterface;

    /**
     * Apply mask
     *
     * @param string $mask
     * @return $this
     */
    public function applyMask(string $mask): SentenceInterface;

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
