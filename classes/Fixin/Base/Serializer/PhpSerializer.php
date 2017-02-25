<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Serializer;

use Throwable;

class PhpSerializer implements SerializerInterface
{
    protected const
        EXCEPTION_SERIALIZATION_FAILED = 'Serialization failed',
        EXCEPTION_UNSERIALIZATION_FAILED = 'Unserialization failed';

    public function serialize($value): string
    {
        try {
            return serialize($value);
        }
        catch (Throwable $t) {
            throw new Exception\RuntimeException(static::EXCEPTION_SERIALIZATION_FAILED, 0, $t);
        }
    }

    public function unserialize(string $data, array $allowedClasses = null)
    {
        try {
            return unserialize($data, isset($allowedClasses) ? ['allowed_classes' => $allowedClasses] : null);
        }
        catch (Throwable $t) {
            throw new Exception\RuntimeException(static::EXCEPTION_UNSERIALIZATION_FAILED, 0, $t);
        }
    }
}
