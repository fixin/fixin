<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Headers;

use Fixin\Resource\PrototypeInterface;

interface HeadersInterface extends PrototypeInterface
{
    public const
        OPTION_VALUES = 'values';

    public function add(string $name, string $value): HeadersInterface;
    public function clear(): HeadersInterface;
    public function get(string $name, array $default = []): array;
    public function has(string $name): bool;
    public function send(): HeadersInterface;
    public function set(string $name, array $values): HeadersInterface;
}
