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
     * @return $this
     */
    public function addParameter($parameter): SentenceInterface;

    /**
     * @return $this
     */
    public function addParameters(array $parameters): SentenceInterface;

    /**
     * @return $this
     */
    public function appendClause(string $clause, string $string): SentenceInterface;

    /**
     * @return $this
     */
    public function appendString(string $string): SentenceInterface;

    /**
     * @return $this
     */
    public function appendWord(string $word): SentenceInterface;

    /**
     * @return $this
     */
    public function applyMask(string $mask): SentenceInterface;

    public function getParameters(): array;
    public function getText(): string;
}
