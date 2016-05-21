<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\ResourceManager;

use Fixin\Base\Exception\InvalidParameterException;
use Fixin\ResourceManager\AbstractFactory\AbstractFactoryInterface;
use Fixin\ResourceManager\Factory\FactoryInterface;
use Fixin\Support\PrototypeInterface;

abstract class ResourceManagerBase implements ResourceManagerInterface {

    const ABSTRACT_FACTORIES_KEY = 'abstractFactories';
    const CLASS_KEY = 'class';
    const DEFINITIONS_KEY = 'definitions';
    const FINAL_KEY = 'final';
    const OPTIONS_KEY = 'options';
    const RESOURCES_KEY = 'resources';

    const CONFIG_INJECT_KEYS = [self::DEFINITIONS_KEY => 'Definition', self::RESOURCES_KEY => 'Resource'];

    const EXCEPTION_ALREADY_DEFINED = "%s already defined for '%'";
    const EXCEPTION_GET_ERRORS = [
        "Resource not accessible by name '%s'",
        "Prototype not accessible by name '%s'",
        "A prototype accessible by name '%s'",
        "A resource accessible by name '%s'",
    ];
    const EXCEPTION_INVALID_ABSTRACT_FACTORY_DEFINITION = "Invalid abstract factory definition '%s'";
    const EXCEPTION_INVALID_DEFINITION = "Invalid definition registered for name '%s'";

    /**
     * Abstract factories
     *
     * @var array
     */
    protected $abstractFactories = [];

    /**
     * Definitions
     *
     * @var array
     */
    protected $definitions = [];

    /**
     * Allocated resources
     *
     * @var array
     */
    protected $resources = [];

    /**
     * Configure
     *
     * @param array $config
     */
    protected function configure(array $config) {
        // Abstract factories
        if (isset($config[static::ABSTRACT_FACTORIES_KEY])) {
            $this->setupAbstractFactories($config[static::ABSTRACT_FACTORIES_KEY]);
        }

        // Inject options
        foreach (static::CONFIG_INJECT_KEYS as $key => $label) {
            isset($config[$key]) && $this->injectOptions($key, $config[$key]);
        }
    }

    /**
     * Create object from definition
     *
     * @param array $definition
     * @return object|NULL
     */
    protected function createFromDefinition(array $definition) {
        if (class_exists($class = $definition[static::CLASS_KEY])) {
            return new $class($this, $definition[static::OPTIONS_KEY] ?? []);
        }

        return null;
    }

    /**
     * Get resource or prototype
     *
     * @param string $name
     * @param bool $prototype
     * @throws Exception\ResourceNotFoundException
     * @return object
     */
    protected function getResource(string $name, bool $prototype) {
        $resource = $this->resources[$name] ?? $this->produceResource($name);

        // Found
        if ($resource && $resource instanceof PrototypeInterface === $prototype) {
            return $resource;
        }

        throw new Exception\ResourceNotFoundException(sprintf(static::EXCEPTION_GET_ERRORS[isset($resource) * 2 + $prototype], $name));
    }

    /**
     * Inject options
     *
     * @param string $key
     * @param array $values
     * @throws Exception\OverrideNotAllowedException
     */
    protected function injectOptions(string $key, array $values) {
        if ($names = array_intersect_key($values, $this->{$key})) {
            throw new Exception\OverrideNotAllowedException(sprintf(static::EXCEPTION_ALREADY_DEFINED, $label, implode("', '", array_keys($names))));
        }

        $this->$key = $values + $this->$key;
    }

    /**
     * Produce resource
     *
     * @param string $name
     * @throws Exception\ResourceFaultException
     * @return object
     */
    protected function produceResource(string $name) {
        $resource = $this->produceResourceFromDefinition(
            $name,
            isset($this->definitions[$name][static::FINAL_KEY])
                ? $this->definitions[$name]
                : $this->resolveDefinition($name)
        );

        // Object
        if (is_object($resource)) {
            $this->resources[$name] = $resource;

            return $resource;
        }

        throw new Exception\ResourceFaultException(sprintf(static::EXCEPTION_INVALID_DEFINITION, $name));
    }

    /**
     * Produce resource from abstract factories
     *
     * @param string $name
     * @param array $options
     * @return mixed|NULL
     */
    protected function produceResourceFromAbstractFactories(string $name, array $options) {
        foreach ($this->abstractFactories as $abstractFactory) {
            if ($abstractFactory->canProduce($this, $name)) {
                return $abstractFactory($this, $options, $name);
            }
        }

        return null;
    }

    /**
     * Produce resource from definition
     *
     * @param string $name
     * @param array $definition
     * @return mixed
     */
    protected function produceResourceFromDefinition(string $name, array $definition) {
        $class = $definition[static::CLASS_KEY];

        if (is_string($class)) {
            $class = $this->createFromDefinition($definition) ?? $this->produceResourceFromAbstractFactories($class, $definition[static::OPTIONS_KEY] ?? []);
        }

        // Factory or Closure
        if ($class instanceof FactoryInterface || $class instanceof \Closure) {
            return $class($this, $definition[static::OPTIONS_KEY] ?? [], $name);
        }

        return $class;
    }

    /**
     * Resolve recursive definitions
     *
     * @param mixed $definition
     * @return array
     */
    protected function resolveDefinition($definition): array {
        // String
        if (is_string($definition)) {
            $definition = [
                static::CLASS_KEY => $definition,
            ];
        }

        // Final definitions
        elseif (!isset($definition[static::CLASS_KEY])) {
            return [
                static::CLASS_KEY => $definition,
                static::FINAL_KEY => true
            ];
        }

        // Resolving recursion
        $class = $definition[static::CLASS_KEY];

        if (isset($this->definitions[$class])) {
            $inherited = $this->definitions[$class];

            if (!isset($inherited[static::FINAL_KEY])) {
                $inherited = $this->definitions[$class] = $this->resolveDefinition($inherited);
            }

            unset($definition[static::CLASS_KEY]);

            return array_replace_recursive($inherited, $definition);
        }

        $definition[static::FINAL_KEY] = true;

        return $definition;
    }

    /**
     * Set abstract factories
     *
     * @param array $abstractFactories
     * @throws InvalidParameterException
     */
    protected function setupAbstractFactories(array $abstractFactories) {
        foreach ($abstractFactories as $key => $abstractFactory) {
            $abstractFactory = $this->createFromDefinition($this->resolveDefinition($abstractFactory));

            if (!$abstractFactory instanceof AbstractFactoryInterface) {
                throw new InvalidParameterException(sprintf(static::EXCEPTION_INVALID_ABSTRACT_FACTORY_DEFINITION, $key));
            }

            $this->abstractFactories[] = $abstractFactory;
        }
    }
}