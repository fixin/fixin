<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\ResourceManager;

use Fixin\Base\Exception\InvalidParameterException;

class ResourceManager extends ResourceManagerBase {

    const EXCEPTION_MUST_BE_OBJECT = 'Resource must be an object.';

    /**
     * @param array $config
     */
    public function __construct(array $config = []) {
        $this->configure($config);
    }

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

    /**
     * Set resource
     *
     * @param string $name
     * @param object $resource
     * @throws InvalidParameterException
     * @return self
     */
    public function setResource(string $name, $resource) {
        if (!is_object($resource)) {
            throw new InvalidParameterException(static::EXCEPTION_MUST_BE_OBJECT);
        }

        $this->configure([static::RESOURCES_KEY => [$name => $resource]]);
        $this->definitions[$name] = true;

        return $this;
    }
}