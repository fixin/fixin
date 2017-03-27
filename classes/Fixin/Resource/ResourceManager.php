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
        CAN_T_USE_PROTOTYPE_AS_RESOURCE_EXCEPTION = "Can't use prototype as resource '%s'",
        CAN_T_USE_RESOURCE_AS_PROTOTYPE_EXCEPTION = "Can't use resource as prototype '%s'",
        PROTOTYPE_NOT_FOUND_EXCEPTION = "Prototype not found by name '%s'",
        RESOURCE_NOT_FOUND_EXCEPTION = "Resource not found by name '%s'";

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
            throw new Exception\ResourceNotFoundException(sprintf(static::CAN_T_USE_RESOURCE_AS_PROTOTYPE_EXCEPTION, $name));
        }

        throw new Exception\ResourceNotFoundException(sprintf(static::PROTOTYPE_NOT_FOUND_EXCEPTION, $name));
    }

    public function get(string $name)
    {
        $resource = $this->resources[$name] ?? $this->produceResource($name);

        if ($resource instanceof ResourceInterface && !$resource instanceof PrototypeInterface) {
            return $resource;
        }

        if (!$resource) {
            throw new Exception\ResourceNotFoundException(sprintf(static::CAN_T_USE_PROTOTYPE_AS_RESOURCE_EXCEPTION, $name));
        }

        throw new Exception\ResourceNotFoundException(sprintf(static::RESOURCE_NOT_FOUND_EXCEPTION, $name));
    }

    public function has(string $name): bool
    {
        return isset($this->definitions[$name]) || class_exists($name) || $this->canProduceByAbstractFactory($name);
    }
}
