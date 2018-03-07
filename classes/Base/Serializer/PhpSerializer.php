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
        SERIALIZATION_FAILURE_EXCEPTION = 'Serialization failed',
        UNSERIALIZATION_FAILURE_EXCEPTION = 'Unserialization failed';

    /**
     * @inheritDoc
     */
    public function serialize($value): string
    {
        try {
            return serialize($value);
        }
        catch (Throwable $t) {
            throw new Exception\SerializationFailureException(static::SERIALIZATION_FAILURE_EXCEPTION, 0, $t);
        }
    }

    /**
     * @inheritDoc
     */
    public function unserialize(string $serialized, array $allowedClasses = null)
    {
        try {
            return unserialize($serialized, isset($allowedClasses) ? ['allowed_classes' => $allowedClasses] : null);
        }
        catch (Throwable $t) {
            throw new Exception\UnserializationFailureException(static::UNSERIALIZATION_FAILURE_EXCEPTION, 0, $t);
        }
    }
}
