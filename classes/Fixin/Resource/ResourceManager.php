<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Resource;

class ResourceManager extends ResourceManagerBase
{
    /**
     * Determine resource creation via an abstract factory is possible
     */
    protected function canProduceByAbstractFactory(string $name): bool
    {
        foreach ($this->abstractFactories as $abstractFactory) {
            if ($abstractFactory->canProduce($name)) {
                return true;
            }
        }

        return false;
    }

    public function clonePrototype(string $name, array $options = []): PrototypeInterface
    {
        return $this->getResource($name, true)->withOptions($options);
    }

    public function get(string $name)
    {
        return $this->getResource($name, false);
    }

    public function has(string $name): bool
    {
        return isset($this->definitions[$name]) || class_exists($name) || $this->canProduceByAbstractFactory($name);
    }
}
