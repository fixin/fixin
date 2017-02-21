<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Resource;

use Fixin\Resource\AbstractFactory\AbstractFactoryInterface;
use Fixin\Resource\Factory\FactoryInterface;

abstract class ResourceManagerBase implements ResourceManagerInterface
{
    protected const
        EXCEPTION_ALREADY_DEFINED = "%s already defined for '%'",
        EXCEPTION_CLASS_NOT_FOUND_FOR = "Class not found for '%s'",
        EXCEPTION_GET_ERRORS = [
            "Resource not accessible by name '%s'",
            "Prototype not accessible by name '%s'",
            "Can't access prototype as normal resource '%s'",
            "Can't access normal resource as prototype '%s'",
        ],
        EXCEPTION_INVALID_ABSTRACT_FACTORY_DEFINITION = "Invalid abstract factory definition '%s'",
        EXCEPTION_INVALID_DEFINITION = "Invalid definition registered for name '%s'",
        KEY_CLASS = 'class',
        KEY_OPTIONS = 'options',
        KEY_RESOLVED = 'resolved';

    public const
        OPTION_ABSTRACT_FACTORIES = 'abstractFactories',
        OPTION_DEFINITIONS = 'definitions',
        OPTION_RESOURCES = 'resources',

        OPTIONS_INJECT_KEYS = [
            self::OPTION_DEFINITIONS => 'Definition',
            self::OPTION_RESOURCES => 'Resource'
        ];

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
        if (isset($options[static::OPTION_ABSTRACT_FACTORIES])) {
            $this->setupAbstractFactories($options[static::OPTION_ABSTRACT_FACTORIES]);
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
        if (class_exists($class = $definition[static::KEY_CLASS])) {
            return new $class($this, $definition[static::KEY_OPTIONS], $name);
        }

        return null;
    }

    /**
     * @throws Exception\ResourceNotFoundException
     * @return object
     */
    protected function getResource(string $name, bool $prototype)
    {
        $resource = $this->resources[$name] ?? $this->produceResource($name);

        // Found
        if ($resource && $resource instanceof PrototypeInterface === $prototype) {
            return $resource;
        }

        throw new Exception\ResourceNotFoundException(sprintf(static::EXCEPTION_GET_ERRORS[isset($resource) * 2 + $prototype], $name));
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
            throw new Exception\ClassNotFoundException(sprintf(static::EXCEPTION_CLASS_NOT_FOUND_FOR, $name));
        }

        throw new Exception\ResourceFaultException(sprintf(static::EXCEPTION_INVALID_DEFINITION, $name));
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
        $class = $definition[static::KEY_CLASS];

        if (is_string($class)) {
            $class = $this->createFromDefinition($name, $definition) ?? $this->produceResourceFromAbstractFactories($class, $definition[static::KEY_OPTIONS]);
        }

        // Factory
        if ($class instanceof FactoryInterface) {
            return $class($definition[static::KEY_OPTIONS], $name);
        }

        // Closure
        if ($class instanceof \Closure) {
            return $class($this, $definition[static::KEY_OPTIONS], $name);
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
            static::KEY_CLASS => $definition,
            static::KEY_OPTIONS => null,
            static::KEY_RESOLVED => true
        ];
    }

    /**
     * Resolve definition (array)
     */
    protected function resolveDefinitionArray(array $definition, string $name): array
    {
        $class = $definition[static::KEY_CLASS] ?? $name;

        if ($class !== $name) {
            $inherited = $this->resolveDefinitionFromName($class);
            unset($definition[static::KEY_CLASS]);

            return array_replace_recursive($inherited, $definition);
        }

        return [
            static::KEY_CLASS => $class,
            static::KEY_OPTIONS => $definition[static::KEY_OPTIONS] ?? null,
            static::KEY_RESOLVED => true
        ];
    }

    protected function resolveDefinitionFromName(string $name): array
    {
        if (isset($this->definitions[$name])) {
            $definition = $this->definitions[$name];

            if (is_array($definition)) {
                if (isset($definition[static::KEY_RESOLVED])) {
                    return $definition;
                }

                return $this->definitions[$name] = $this->resolveDefinitionArray($definition, $name);
            }

            return $this->definitions[$name] = $this->resolveDefinition($definition, $name);
        }

        return [
            static::KEY_CLASS => $name,
            static::KEY_OPTIONS => null,
            static::KEY_RESOLVED => true
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
                throw new Exception\InvalidArgumentException(sprintf(static::EXCEPTION_INVALID_ABSTRACT_FACTORY_DEFINITION, $key));
            }

            $this->abstractFactories[] = $abstractFactory;
        }
    }
}
