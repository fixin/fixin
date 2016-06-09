<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Serializer;

use Fixin\Exception\RuntimeException;

class PhpSerializer implements SerializerInterface {

    const EXCEPTION_SERIALIZATION_FAILED = 'Serialization failed';
    const EXCEPTION_UNSERIALIZATION_FAILED = 'Unserialization failed';

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Serializer\SerializerInterface::serialize($value)
     */
    public function serialize($value): string {
        try {
            return serialize($value);
        }
        catch (\Throwable $t) {
            throw new RuntimeException(static::EXCEPTION_SERIALIZATION_FAILED, 0, $t);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Serializer\SerializerInterface::unserialize($data, $allowedClasses)
     */
    public function unserialize(string $data, array $allowedClasses = null) {
        try {
            return unserialize($value, isset($allowedClasses) ? ['allowed_classes' => $allowedClasses] : null);
        }
        catch (\Throwable $t) {
            throw new RuntimeException(static::EXCEPTION_UNSERIALIZATION_FAILED, 0, $t);
        }
    }
}