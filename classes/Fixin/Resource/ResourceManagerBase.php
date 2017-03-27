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

abstract class ResourceManagerBase implements ResourceManagerInterface
{
    protected const
        ALREADY_DEFINED_EXCEPTION = "%s already defined for '%'",
        CLASS_NOT_FOUND_FOR_EXCEPTION = "Class not found for '%s'",
        INVALID_ABSTRACT_FACTORY_DEFINITION_EXCEPTION = "Invalid abstract factory definition '%s'",
        INVALID_DEFINITION_EXCEPTION = "Invalid definition registered for name '%s'",

        CLASS_KEY = 'class',
        OPTIONS_KEY = 'options',
        RESOLVED_KEY = 'resolved',

        OPTIONS_INJECT_KEYS = [
            self::DEFINITIONS => 'Definition',
            self::RESOURCES => 'Resource'
        ];

    public const
        ABSTRACT_FACTORIES = 'abstractFactories',
        DEFINITIONS = 'definitions',
        RESOURCES = 'resources';

    /**
     * @var array
     */
    protected $abstractFactories = [];

    /**
     * @var array
     */
    protected $definitions = [];

    /**
     * @var array
     */
    protected $resources = [];

    public function __construct(array $options)
    {
        // Abstract factories
        if (isset($options[static::ABSTRACT_FACTORIES])) {
            $this->setupAbstractFactories($options[static::ABSTRACT_FACTORIES]);
        }

        // Inject options
        foreach (static::OPTIONS_INJECT_KEYS as $key => $label) {
            if (isset($options[$key])) {
                $this->$key = $options[$key];
            }
        }
    }

    public function __toString(): string
    {
        $resources = [];

        foreach ($this->definitions as $key => $definition) {
            $resources[$key] = str_pad($key, 50) . ' defined';
        }

        foreach ($this->resources as $key => $resource) {
            $resources[$key] = str_pad($key, 50) . ' {' . get_class($resource) . '} ' . ($resource instanceof PrototypeInterface ? 'prototype' : 'resource');
        }

        ksort($resources);

        return get_class($this) . " {\n\n    " . implode(",\n    ", $resources) . "\n}";
    }

    /**
     * Create object from definition
     *
     * @return object|null
     */
    protected function createFromDefinition(string $name, array $definition)
    {
        if (class_exists($class = $definition[static::CLASS_KEY])) {
            return new $class($this, $definition[static::OPTIONS_KEY], $name);
        }

        return null;
    }

    /**
     * @return object
     * @throws Exception\ClassNotFoundException
     * @throws Exception\ResourceFaultException
     */
    protected function produceResource(string $name)
    {
        $resource = $this->produceResourceFromDefinition($name, $this->resolveDefinitionFromName($name));

        // Object
        if (is_object($resource)) {
            $this->resources[$name] = $resource;

            return $resource;
        }

        // Null
        if (is_null($resource)) {
            throw new Exception\ClassNotFoundException(sprintf(static::CLASS_NOT_FOUND_FOR_EXCEPTION, $name));
        }

        throw new Exception\ResourceFaultException(sprintf(static::INVALID_DEFINITION_EXCEPTION, $name));
    }

    /**
     * @return mixed|NULL
     */
    protected function produceResourceFromAbstractFactories(string $name, array $options = null)
    {
        foreach ($this->abstractFactories as $abstractFactory) {
            if ($abstractFactory->canProduce($name)) {
                return $abstractFactory($options, $name);
            }
        }

        return null;
    }

    /**
     * @return mixed
     */
    protected function produceResourceFromDefinition(string $name, array $definition)
    {
        $class = $definition[static::CLASS_KEY];

        if (is_string($class)) {
            $class = $this->createFromDefinition($name, $definition) ?? $this->produceResourceFromAbstractFactories($class, $definition[static::OPTIONS_KEY]);
        }

        // Factory
        if ($class instanceof FactoryInterface) {
            return $class($definition[static::OPTIONS_KEY], $name);
        }

        // Closure
        if ($class instanceof \Closure) {
            return $class($this, $definition[static::OPTIONS_KEY], $name);
        }

        return $class;
    }

    /**
     * Resolve definition (various data)
     */
    protected function resolveDefinition($definition, string $name): array
    {
        // String
        if (is_string($definition)) {
            return $this->resolveDefinitionFromName($definition);
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

    /**
     * Resolve definition (array)
     */
    protected function resolveDefinitionArray(array $definition, string $name): array
    {
        $class = $definition[static::CLASS_KEY] ?? $name;

        if ($class !== $name) {
            $inherited = $this->resolveDefinitionFromName($class);
            unset($definition[static::CLASS_KEY]);

            return array_replace_recursive($inherited, $definition);
        }

        return [
            static::CLASS_KEY => $class,
            static::OPTIONS_KEY => $definition[static::OPTIONS_KEY] ?? null,
            static::RESOLVED_KEY => true
        ];
    }

    protected function resolveDefinitionFromName(string $name): array
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
            $abstractFactory = $this->createFromDefinition($key, $this->resolveDefinition($abstractFactory, ''));

            if (!$abstractFactory instanceof AbstractFactoryInterface) {
                throw new Exception\InvalidArgumentException(sprintf(static::INVALID_ABSTRACT_FACTORY_DEFINITION_EXCEPTION, $key));
            }

            $this->abstractFactories[] = $abstractFactory;
        }
    }
}
