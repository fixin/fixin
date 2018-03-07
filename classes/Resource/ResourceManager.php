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
    public const
        ABSTRACT_FACTORIES = 'abstractFactories',
        DEFINITIONS = 'definitions',
        RESOURCES = 'resources';

    protected const
        CLASS_KEY = 'class',
        CLASS_NOT_FOUND_EXCEPTION = "Class not found '%s'",
        INJECT_KEYS = [
            self::DEFINITIONS,
            self::RESOURCES,
        ],
        INVALID_ABSTRACT_FACTORY_DEFINITION_EXCEPTION = "Invalid abstract factory definition '%s'",
        OPTIONS_KEY = 'options',
        RESOURCE_AS_PROTOTYPE_EXCEPTION = "Can't use resource as prototype '%s'",
        RESOURCE_EXPECTED_EXCEPTION = "Resource expected '%s'",
        UNEXPECTED_RESOURCE_EXCEPTION = "Unexpected resource for name '%s' (%s), '%s' expected";

    /**
     * @var AbstractFactory
     */
    protected $abstractFactoryChain;

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

    /**
     * ResourceManager constructor.
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        // Inject options
        foreach (static::INJECT_KEYS as $key) {
            $this->$key = $options[$key] ?? [];
        }

        // Abstract factories
        if (isset($options[static::ABSTRACT_FACTORIES])) {
            $this->prepareAbstractFactoryChain($options[static::ABSTRACT_FACTORIES]);
        }
    }

    /**
     * @inheritDoc
     */
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

    /**
     * Determine if can produce by abstract factories
     *
     * @param string $key
     * @return bool
     */
    protected function canProduceByAbstractFactories(string $key): bool
    {
        return $this->abstractFactoryChain->canChainProduce($key);
    }

    /**
     * @inheritDoc
     */
    public function clone(string $name, string $expectedClass, array $options = []): object
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
     * @inheritDoc
     */
    public function get(string $name, string $expectedClass): ResourceInterface
    {
        $resource = $this->prepareResource($name, $expectedClass);

        if ($resource instanceof ResourceInterface) {
            return $resource;
        }

        throw new Exception\ResourceNotFoundException(sprintf(static::RESOURCE_EXPECTED_EXCEPTION, $name));
    }

    /**
     * @inheritDoc
     */
    public function has(string $name): bool
    {
        return $this->hasTests[$name] ?? $this->hasTests[$name] = isset($this->resources[$name]) || isset($this->definitions[$name]) || $this->canProduceByAbstractFactories($name);
    }

    /**
     * Prepare abstract factory chain
     *
     * @param array $definitions
     * @throws Exception\InvalidArgumentException
     */
    protected function prepareAbstractFactoryChain(array $definitions): void
    {
        foreach (array_reverse($definitions) as $key => $definition) {
            $class = $definition[static::CLASS_KEY] ?? $definition;

            $abstractFactory = new $class($this, [AbstractFactory::NEXT => $this->abstractFactoryChain] + ($definition[static::OPTIONS_KEY] ?? []), $key);

            if (!$abstractFactory instanceof AbstractFactory) {
                throw new Exception\InvalidArgumentException(sprintf(static::INVALID_ABSTRACT_FACTORY_DEFINITION_EXCEPTION, $key));
            }

            $this->abstractFactoryChain = $abstractFactory;
        }
    }

    /**
     * Prepare resource
     *
     * @param string $name
     * @param string $expectedClass
     * @return object
     */
    protected function prepareResource(string $name, string $expectedClass): object
    {
        $resource = $this->resources[$name] ?? $this->resources[$name] = $this->produceResource($name, []);

        if ($resource instanceof $expectedClass) {
            return $resource;
        }

        throw new Exception\UnexpectedResourceException(sprintf(static::UNEXPECTED_RESOURCE_EXCEPTION, $name, $resource ? get_class($resource) : 'null', $expectedClass));
    }

    /**
     * Produce resource
     *
     * @param string $key
     * @param array $options
     * @return object
     */
    protected function produceResource(string $key, array $options): object
    {
        if (isset($this->definitions[$key])) {
            $definition = $this->definitions[$key];

            if (is_string($definition)) {
                return $this->produceResource($definition, $options);
            }
            elseif (is_array($definition)) {
                if (isset($definition[static::OPTIONS_KEY])) {
                    $options += $definition[static::OPTIONS_KEY];
                }

                if (isset($definition[static::CLASS_KEY])) {
                    return $this->produceResource($definition[static::CLASS_KEY], $options);
                }
            }
            elseif ($definition instanceof \Closure) {
                return $definition($this, $options);
            }
        }

        $instance = $this->abstractFactoryChain->chainProduce($key, $options);

        if ($instance === null) {
            throw new Exception\ClassNotFoundException(sprintf(static::CLASS_NOT_FOUND_EXCEPTION, $key));
        }

        return $instance instanceof FactoryInterface || $instance instanceof \Closure ? $instance($this, $options) : $instance;
    }
}
