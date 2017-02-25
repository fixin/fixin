<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Sentence;

use Fixin\Resource\PrototypeInterface;

interface SentenceInterface extends PrototypeInterface
{
    public function addParameter($parameter): SentenceInterface;
    public function addParameters(array $parameters): SentenceInterface;
    public function appendClause(string $clause, string $string): SentenceInterface;
    public function appendString(string $string): SentenceInterface;
    public function appendWord(string $word): SentenceInterface;
    public function applyMask(string $mask): SentenceInterface;
    public function getParameters(): array;
    public function getText(): string;
}
