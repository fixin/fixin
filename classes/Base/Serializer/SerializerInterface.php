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
    /**
     * Serialize value
     *
     * @param $value
     * @return string
     */
    public function serialize($value): string;

    /**
     * Create value from a serialized string
     *
     * @param string $serialized
     * @param array|null $allowedClasses
     * @return mixed
     */
    public function unserialize(string $serialized, array $allowedClasses = null);
}
