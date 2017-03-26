<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Serializer;

use Fixin\Resource\ResourceInterface;

interface SerializerInterface extends ResourceInterface
{
    public function serialize($value): string;

    /**
     * Create value from a serialized string
     */
    public function unserialize(string $serialized, array $allowedClasses = null);
}
