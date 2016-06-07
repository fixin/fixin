<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Resource;

class ResourceManager extends ResourceManagerBase {

    /**
     * Determine resource creation via an abstract factory is possible
     *
     * @param string $name
     * @return bool
     */
    protected function canProduceByAbstractFactory(string $name): bool {
        foreach ($this->abstractFactories as $abstractFactory) {
            if ($abstractFactory->canProduce($name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Resource\ResourceManagerInterface::clonePrototype()
     */
    public function clonePrototype(string $name, array $options = []) {
        return $this->getResource($name, true)->withOptions($options);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Support\ContainerInterface::get($name)
     */
    public function get(string $name) {
        return $this->getResource($name, false);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Support\ContainerInterface::has($name)
     */
    public function has(string $name): bool {
        return isset($this->definitions[$name]) || class_exists($name) || $this->canProduceByAbstractFactory($name);
    }
}