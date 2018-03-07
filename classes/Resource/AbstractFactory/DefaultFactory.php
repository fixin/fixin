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
    /**
     * @inheritDoc
     */
    protected function canProduce(string $key): bool
    {
        return class_exists($key);
    }

    /**
     * @inheritDoc
     */
    protected function produce(string $key, array $options)
    {
        // TODO: check of ResourceInterface?
        return new $key($this->resourceManager, $options);
    }
}
