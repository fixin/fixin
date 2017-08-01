<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Resource\AbstractFactory;

class DefaultFactory extends AbstractFactory
{
    public function canProduce(string $key): bool
    {
        return class_exists($key);
    }

    public function produce(string $key, array $options, string $name)
    {
        return new $key($this->resourceManager, $options, $name);
    }
}
