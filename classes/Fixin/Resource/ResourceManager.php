<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Resource;

use Fixin\Resource\AbstractFactory\AbstractFactoryInterface;

class ResourceManager extends ResourceManagerBase
{
    protected const
        EXCEPTION_RESOURCE_NOT_FOUND = "Resource not found by name '%s'",
        EXCEPTION_PROTOTYPE_NOT_FOUND = "Prototype not found by name '%s'",
        EXCEPTION_PROTOTYPE_AS_RESOURCE = "Can't use prototype as resource '%s'",
        EXCEPTION_RESOURCE_AS_PROTOTYPE = "Can't use resource as prototype '%s'";

    /**
     * Determine resource creation via an abstract factory is possible
     */
    protected function canProduceByAbstractFactory(string $name): bool
    {
        /** @var AbstractFactoryInterface $abstractFactory */
        foreach ($this->abstractFactories as $abstractFactory) {
            if ($abstractFactory->canProduce($name)) {
                return true;
            }
        }

        return false;
    }

    public function clone(string $name, array $options = [])
    {
        $resource = $this->resources[$name] ?? $this->produceResource($name);

        if ($resource instanceof PrototypeInterface) {
            return $resource->withOptions($options);
        }

        if (!$resource instanceof ResourceInterface) {
            return clone $resource;
        }

        if (!$resource) {
            throw new Exception\ResourceNotFoundException(sprintf(static::EXCEPTION_RESOURCE_AS_PROTOTYPE, $name));
        }

        throw new Exception\ResourceNotFoundException(sprintf(static::EXCEPTION_PROTOTYPE_NOT_FOUND, $name));
    }

    public function get(string $name)
    {
        $resource = $this->resources[$name] ?? $this->produceResource($name);

        if ($resource instanceof ResourceInterface && !$resource instanceof PrototypeInterface) {
            return $resource;
        }

        if (!$resource) {
            throw new Exception\ResourceNotFoundException(sprintf(static::EXCEPTION_PROTOTYPE_AS_RESOURCE, $name));
        }

        throw new Exception\ResourceNotFoundException(sprintf(static::EXCEPTION_RESOURCE_NOT_FOUND, $name));
    }

    public function has(string $name): bool
    {
        return isset($this->definitions[$name]) || class_exists($name) || $this->canProduceByAbstractFactory($name);
    }
}
