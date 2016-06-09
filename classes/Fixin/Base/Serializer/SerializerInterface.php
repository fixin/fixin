<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Serializer;

interface SerializerInterface {

    /**
     * Serialize value
     *
     * @param mixed $value
     * @return string
     */
    public function serialize($value): string;

    /**
     * Create value from a serialized string
     *
     * @param string $data
     * @param array $allowedClasses
     * @return mixed
     */
    public function unserialize(string $data, array $allowedClasses = null);
}