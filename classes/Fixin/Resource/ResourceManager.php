<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Resource;

class ResourceManager extends ResourceManagerBase {

    /**
     * {@inheritDoc}
     * @see \Fixin\Resource\ResourceManagerInterface::clonePrototype()
     */
    public function clonePrototype(string $name, array $options = []) {
        return $this->getResource($name, true)->withOptions($options);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Resource\ResourceManagerInterface::get($name)
     */
    public function get(string $name) {
        return $this->getResource($name, false);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Resource\ResourceManagerInterface::has($name)
     */
    public function has(string $name): bool {
        return isset($this->definitions[$name]) || class_exists($name) || $this->canProduceByAbstractFactory($name);
    }
}