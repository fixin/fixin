<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\ResourceManager;

class ResourceManager extends ResourceManagerBase {

    const EXCEPTION_MUST_BE_OBJECT = 'Resource must be an object.';

    /**
     * {@inheritDoc}
     * @see \Fixin\ResourceManager\ResourceManagerInterface::clonePrototype()
     */
    public function clonePrototype(string $name) {
        return clone $this->getResource($name, true);
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
        // Made or defined
        if (isset($this->definitions[$name])) {
            return true;
        }

        // Abstract factories
        $has = false;

        foreach ($this->abstractFactories as $abstractFactory) {
            if ($abstractFactory->canProduce($this, $name)) {
                $has = true;
                break;
            }
        }

        return $has;
    }
}