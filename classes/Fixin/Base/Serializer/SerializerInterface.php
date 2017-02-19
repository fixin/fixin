<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Serializer;

interface SerializerInterface
{
    public function serialize($value): string;

    /**
     * Create value from a serialized string
     */
    public function unserialize(string $data, array $allowedClasses = null);
}
