<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Resource;

use Fixin\Resource\AbstractFactory\AbstractFactory;
use Fixin\Support\Ground;

class ResourceManager implements ResourceManagerInterface
{
    protected const
        CLASS_KEY = 'class',
        CLASS_NOT_FOUND_EXCEPTION = "Class not found for '%s'",
        INJECT_KEYS = [
            self::DEFINITIONS,
            self::RESOURCES,
        ],
        INVALID_ABSTRACT_FACTORY_DEFINITION_EXCEPTION = "Invalid abstract factory definition '%s'",
        OPTIONS_KEY = 'options',
        PROTOTYPE_AS_RESOURCE_EXCEPTION = "Can't use prototype as resource '%s'",
        PROTOTYPE_NOT_FOUND_EXCEPTION = "Prototype not found by name '%s'",
        RESOURCE_AS_PROTOTYPE_EXCEPTION = "Can't use resource as prototype '%s'",
        RESOURCE_NOT_FOUND_EXCEPTION = "Resource not found by name '%s'",
        UNEXPECTED_RESOURCE_EXCEPTION = "Unexpected resource for name '%s' (%s), '%s' expected";

    public const
        ABSTRACT_FACTORIES = 'abstractFactories',
        DEFINITIONS = 'definitions',
        RESOURCES = 'resources';

    /**
     * @var AbstractFactory
     */
    protected $abstractFactoryChain;

    /**
     * @var string[][]
     */
    protected $abstractFactoryDefinitions = [];

    /**
     * @var array
     */
    protected $abstractFactoryMappings = [];

    /**
     * @var array
     */
    protected $definitions;

    /**
     * @var bool[]
     */
    protected $hasTests = [];

    /**
     * @var array
     */
    protected $resources;

    public function __construct(array $options)
    {
        // Abstract factories
        if (isset($options[static::ABSTRACT_FACTORIES])) {
            $this->abstractFactoryDefinitions = $options[static::ABSTRACT_FACTORIES];
        }

        // Inject options
        foreach (static::INJECT_KEYS as $key) {
            $this->$key = $options[$key] ?? [];
        }
    }

    public function __toString(): string
    {
        $resources = [];

        foreach ($this->definitions as $key => $definition) {
            $resources[$key] = str_pad($key, 50) . ' defined';
        }

        foreach ($this->resources as $key => $resource) {
            $resources[$key] = str_pad($key, 50) . ' {' . get_class($resource) . '} ' . ($resource instanceof ResourceInterface ? 'resource' : 'prototype');
        }

        ksort($resources);

        return Ground::toDebugBlock(get_class($this) . ' {' . PHP_EOL . PHP_EOL . '    ' . implode(',' . PHP_EOL . '    ', $resources) . PHP_EOL . '}');
    }

    protected function canProduceByAbstractFactories(string $key): bool
    {
        return ($this->abstractFactoryChain ?? $this->prepareAbstractFactoryChain())->canChainProduce($key);
    }

    /**
     * @throws Exception\ResourceNotFoundException
     */
    public function clone(string $name, string $expectedClass, array $options = [])
    {
        $resource = $this->prepareResource($name, $expectedClass);

        if ($resource instanceof PrototypeInterface) {
            return $resource->withOptions($options);
        }

        if (!$resource instanceof ResourceInterface) {
            return clone $resource;
        }

        throw new Exception\ResourceNotFoundException(sprintf(static::RESOURCE_AS_PROTOTYPE_EXCEPTION, $name));
    }

    /**
     * @throws Exception\ResourceNotFoundException
     */
    public function get(string $name, string $expectedClass)
    {
        $resource = $this->prepareResource($name, $expectedClass);

        if ($resource instanceof ResourceInterface) {
            return $resource;
        }

        throw new Exception\ResourceNotFoundException(sprintf(static::PROTOTYPE_AS_RESOURCE_EXCEPTION, $name));
    }

    public function has(string $name): bool
    {
        return $this->hasTests[$name] ?? $this->hasTests[$name] = isset($this->resources[$name]) || isset($this->definitions[$name]) || $this->canProduceByAbstractFactories($name);
    }

    protected function prepareAbstractFactoryChain(): AbstractFactory
    {
        $definitions = $this->abstractFactoryDefinitions;
        $this->abstractFactoryDefinitions = [];

        foreach (array_reverse($definitions) as $key => $definition) {
            $class = $definition[static::CLASS_KEY] ?? $definition;

            $abstractFactory = new $class($this, [AbstractFactory::NEXT => $this->abstractFactoryChain] + ($definition[static::OPTIONS_KEY] ?? []), $key);

            if (!$abstractFactory instanceof AbstractFactory) {
                throw new Exception\InvalidArgumentException(sprintf(static::INVALID_ABSTRACT_FACTORY_DEFINITION_EXCEPTION, $key));
            }

            $this->abstractFactoryChain = $abstractFactory;
        }

        return $this->abstractFactoryChain;
    }

    protected function prepareResource(string $name, string $expectedClass)
    {
        $resource = $this->resources[$name] ?? $this->resources[$name] = $this->produceResource($name, [], $name);

        if ($resource instanceof $expectedClass) {
            return $resource;
        }

        throw new Exception\UnexpectedResourceException(sprintf(static::UNEXPECTED_RESOURCE_EXCEPTION, $name, $resource ? get_class($resource) : 'null', $expectedClass));
    }

    protected function produceResource(string $key, array $options, string $name)
    {
        if (isset($this->definitions[$key])) {
            $definition = $this->definitions[$key];

            if (is_string($definition)) {
                return $this->produceResource($definition, $options, $name);
            }
            elseif (is_array($definition)) {
                if (isset($definition[static::OPTIONS_KEY])) {
                    $options += $definition[static::OPTIONS_KEY];
                }

                if (isset($definition[static::CLASS_KEY])) {
                    return $this->produceResource($definition[static::CLASS_KEY], $options, $name);
                }
            }
            elseif ($definition instanceof \Closure) {
                return $definition($this, $options, $key);
            }
        }

        $instance = ($this->abstractFactoryChain ?? $this->prepareAbstractFactoryChain())->chainProduce($key, $options, $name);

        if ($instance === null) {
            throw new Exception\ClassNotFoundException(sprintf(static::CLASS_NOT_FOUND_EXCEPTION, $name));
        }

        return $instance instanceof FactoryInterface || $instance instanceof \Closure ? $instance($this, $options, $name) : $instance;
    }
}
