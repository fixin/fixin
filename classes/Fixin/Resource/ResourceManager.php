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

class ResourceManager implements ResourceManagerInterface
{
    protected const
        CAN_T_USE_PROTOTYPE_AS_RESOURCE_EXCEPTION = "Can't use prototype as resource '%s'",
        CAN_T_USE_RESOURCE_AS_PROTOTYPE_EXCEPTION = "Can't use resource as prototype '%s'",
        CLASS_KEY = 'class',
        CLASS_NOT_FOUND_EXCEPTION = "Class not found for '%s'",
        INJECT_KEYS = [
            self::DEFINITIONS,
            self::RESOURCES,
        ],
        INVALID_ABSTRACT_FACTORY_DEFINITION_EXCEPTION = "Invalid abstract factory definition '%s'",
        INVALID_DEFINITION_EXCEPTION = "Invalid definition registered for name '%s'",
        OPTIONS_KEY = 'options',
        PROTOTYPE_NOT_FOUND_EXCEPTION = "Prototype not found by name '%s'",
        RESOLVED_KEY = 'resolved',
        RESOURCE_NOT_FOUND_EXCEPTION = "Resource not found by name '%s'",
        UNEXPECTED_RESOURCE_EXCEPTION = "Unexpected resource for name '%s', '%s' expected";

    public const
        ABSTRACT_FACTORIES = 'abstractFactories',
        DEFINITIONS = 'definitions',
        RESOURCES = 'resources';

    /**
     * @var AbstractFactoryInterface[]
     */
    protected $abstractFactories = [];

    /**
     * @var array
     */
    protected $definitions;

    /**
     * @var ResourceInterface[]
     */
    protected $resources;

    public function __construct(array $options)
    {
        // Abstract factories
        if (isset($options[static::ABSTRACT_FACTORIES])) {
            $this->setupAbstractFactories($options[static::ABSTRACT_FACTORIES]);
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

        return get_class($this) . ' {' . PHP_EOL . PHP_EOL . '    ' . implode(',' . PHP_EOL . '    ', $resources) . PHP_EOL . '}';
    }

    protected function canProduceByAbstractFactory(string $name): bool
    {
        foreach ($this->abstractFactories as $abstractFactory) {
            if ($abstractFactory->canProduce($name)) {
                return true;
            }
        }

        return false;
    }

    public function clone(string $name, string $class, array $options = [])
    {
        $resource = $this->prepareResource($name, $class);

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

    /**
     * @return object|null
     */
    protected function createFromDefinition(string $name, array $definition)
    {
        if (class_exists($class = $definition[static::CLASS_KEY])) {
            return new $class($this, $definition[static::OPTIONS_KEY], $name);
        }

        return null;
    }

    public function get(string $name, string $class): ?ResourceInterface
    {
        $resource = $this->prepareResource($name, $class);

        if ($resource instanceof ResourceInterface) {
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

    protected function prepareResource(string $name, string $class)
    {
        $resource = $this->resources[$name] ?? $this->produceResource($name);

        if ($resource instanceof $class) {
            return $class;
        }

        throw new Exception\UnexpectedResourceException(sprintf(static::UNEXPECTED_RESOURCE_EXCEPTION, $name, $class));
    }

    /**
     * @return object
     * @throws Exception\ClassNotFoundException
     * @throws Exception\ResourceFaultException
     */
    protected function produceResource(string $name)
    {
        $resource = $this->produceResourceFromDefinition($name, $this->resolveDefinitionName($name));

        // Object
        if (is_object($resource)) {
            $this->resources[$name] = $resource;

            return $resource;
        }

        // Null
        if (is_null($resource)) {
            throw new Exception\ClassNotFoundException(sprintf(static::CLASS_NOT_FOUND_EXCEPTION, $name));
        }

        throw new Exception\ResourceFaultException(sprintf(static::INVALID_DEFINITION_EXCEPTION, $name));
    }

    protected function produceResourceFromAbstractFactories(string $name, array $options = null)
    {
        foreach ($this->abstractFactories as $abstractFactory) {
            if ($abstractFactory->canProduce($name)) {
                return $abstractFactory($options, $name);
            }
        }

        return null;
    }

    protected function produceResourceFromDefinition(string $name, array $definition)
    {
        $class = $definition[static::CLASS_KEY];

        // Name
        if (is_string($class)) {
            $class = $this->createFromDefinition($name, $definition) ?? $this->produceResourceFromAbstractFactories($class, $definition[static::OPTIONS_KEY]);
        }

        // Factory, Closure
        if ($class instanceof FactoryInterface || $class instanceof \Closure) {
            return $class($this, $definition[static::OPTIONS_KEY], $name);
        }

        return $class;
    }

    protected function resolveDefinition($definition, string $name): array
    {
        // String
        if (is_string($definition)) {
            return $this->resolveDefinitionName($definition);
        }
        // Array
        elseif (is_array($definition)) {
            return $this->resolveDefinitionArray($definition, $name);
        }

        return [
            static::CLASS_KEY => $definition,
            static::OPTIONS_KEY => null,
            static::RESOLVED_KEY => true
        ];
    }

    protected function resolveDefinitionArray(array $definition, string $name): array
    {
        $class = $definition[static::CLASS_KEY] ?? $name;

        if ($class !== $name) {
            $inherited = $this->resolveDefinitionName($class);
            unset($definition[static::CLASS_KEY]);

            return array_replace_recursive($inherited, $definition);
        }

        return [
            static::CLASS_KEY => $class,
            static::OPTIONS_KEY => $definition[static::OPTIONS_KEY] ?? null,
            static::RESOLVED_KEY => true
        ];
    }

    protected function resolveDefinitionName(string $name): array
    {
        if (isset($this->definitions[$name])) {
            $definition = $this->definitions[$name];

            if (is_array($definition)) {
                if (isset($definition[static::RESOLVED_KEY])) {
                    return $definition;
                }

                return $this->definitions[$name] = $this->resolveDefinitionArray($definition, $name);
            }

            return $this->definitions[$name] = $this->resolveDefinition($definition, $name);
        }

        return [
            static::CLASS_KEY => $name,
            static::OPTIONS_KEY => null,
            static::RESOLVED_KEY => true
        ];
    }

    /**
     * @throws Exception\InvalidArgumentException
     */
    protected function setupAbstractFactories(array $abstractFactories): void
    {
        foreach ($abstractFactories as $key => $abstractFactory) {
            $abstractFactory = $this->createFromDefinition($key, $this->resolveDefinitionArray($abstractFactory, ''));

            if (!$abstractFactory instanceof AbstractFactoryInterface) {
                throw new Exception\InvalidArgumentException(sprintf(static::INVALID_ABSTRACT_FACTORY_DEFINITION_EXCEPTION, $key));
            }

            $this->abstractFactories[] = $abstractFactory;
        }
    }
}
