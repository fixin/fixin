<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Serializer;

use Fixin\Resource\Resource;
use Throwable;

class PhpSerializer extends Resource implements SerializerInterface
{
    protected const
        SERIALIZATION_FAILED_EXCEPTION = 'Serialization failed',
        UNSERIALIZATION_FAILED_EXCEPTION = 'Unserialization failed';

    public function serialize($value): string
    {
        try {
            return serialize($value);
        }
        catch (Throwable $t) {
            throw new Exception\RuntimeException(static::SERIALIZATION_FAILED_EXCEPTION, 0, $t);
        }
    }

    public function unserialize(string $serialized, array $allowedClasses = null)
    {
        try {
            return unserialize($serialized, isset($allowedClasses) ? ['allowed_classes' => $allowedClasses] : null);
        }
        catch (Throwable $t) {
            throw new Exception\RuntimeException(static::UNSERIALIZATION_FAILED_EXCEPTION, 0, $t);
        }
    }
}
