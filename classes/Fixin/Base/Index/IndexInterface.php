<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Index;

use Fixin\Resource\PrototypeInterface;

interface IndexInterface extends PrototypeInterface
{
    public function clear(): IndexInterface;

    public function getKeysOf($value): array;
    public function getKeysOfGreaterThan($value): array;
    public function getKeysOfGreaterThanOrEqual($value): array;
    public function getKeysOfInterval($beginValue, $endValue): array;
    public function getKeysOfLowerThan($value): array;
    public function getKeysOfLowerThanOrEqual($value): array;
    public function getKeysOfValues(array $values): array;
    public function getValue($key);
    public function getValues(array $keys): array;

    /**
     * Rollback modifications to the last saved state
     */
    public function revert(): IndexInterface;

    /**
     * Write data if dirty
     */
    public function save(): IndexInterface;

    public function set($key, $value): IndexInterface;
    public function unset($key): IndexInterface;
}
