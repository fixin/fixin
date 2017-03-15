<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Http;

use Fixin\Resource\PrototypeInterface;

interface HttpHeadersInterface extends PrototypeInterface
{
    public const
        OPTION_VALUES = 'values';

    public function add(string $name, string $value): HttpHeadersInterface;
    public function clear(): HttpHeadersInterface;
    public function get(string $name, array $default = []): array;
    public function has(string $name): bool;
    public function send(): HttpHeadersInterface;
    public function set(string $name, array $values): HttpHeadersInterface;
}
