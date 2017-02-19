<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Query;

use Fixin\Resource\PrototypeInterface;

interface QueryInterface extends PrototypeInterface
{
    public function addParameter($parameter): QueryInterface;
    public function addParameters(array $parameters): QueryInterface;
    public function appendClause(string $clause, string $string): QueryInterface;
    public function appendString(string $string): QueryInterface;
    public function appendWord(string $word): QueryInterface;
    public function applyMask(string $mask): QueryInterface;
    public function getParameters(): array;
    public function getText(): string;
}
