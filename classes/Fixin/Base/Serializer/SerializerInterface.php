<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Serializer;

use Fixin\Resource\ResourceInterface;

interface SerializerInterface extends ResourceInterface
{
    public function serialize($value): string;

    /**
     * Create value from a serialized string
     */
    public function unserialize(string $data, array $allowedClasses = null);
}
