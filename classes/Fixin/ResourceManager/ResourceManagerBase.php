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
    const GET_ERRORS = [
        'Resource not',
        'Prototype not',
        'Prototype',
        'Resource',
    ];
    const OPTIONS_KEY = 'options';
    const RESOURCES_KEY = 'resources';

    const CONFIG_INJECT_KEYS = [self::DEFINITIONS_KEY => 'Definition', self::RESOURCES_KEY => 'Resource'];

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
            return new $class($this, $definition[static::OPTIONS_KEY]);
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

        throw new Exception\ResourceNotFoundException(static::GET_ERRORS[isset($resource) * 2 + $prototype] . " accessible with name '$name'");
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
            throw new Exception\OverrideNotAllowedException("$label already defined for '" . implode("', '", array_keys($names)) . "'");
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
        $resource = $this->produceResourceFromDefinition($name, $this->resolveDefinition($name));

        // Object
        if (is_object($resource)) {
            $this->resources[$name] = $resource;
            $this->definitions[$name] = true;

            return $resource;
        }

        // Store result
        $this->definitions[$name] = false;

        throw new Exception\ResourceFaultException("Invalid definition registered for name '$name'");
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
     * @param mixed $definition
     * @return mixed
     */
    protected function produceResourceFromDefinition(string $name, $definition) {
        $options = null;

        if (is_array($definition)) {
            $options = $definition[static::OPTIONS_KEY] ?? [];
            $definition = $this->createFromDefinition($definition) ?? $this->produceResourceFromAbstractFactories($definition[static::CLASS_KEY], $options);
        }

        // Factory or Closure
        if ($definition instanceof FactoryInterface || $definition instanceof \Closure) {
            $definition = $definition($this, $options, $name);
        }

        return $definition;
    }

    /**
     * Resolve array definition
     *
     * @param array $definition
     * @return mixed
     */
    protected function resolveArrayDefinition(array $definition) {
        $inherited = $this->resolveDefinition($definition[static::CLASS_KEY]);

        if (is_array($inherited)) {
            unset($definition[static::CLASS_KEY]);

            return array_replace_recursive($inherited, $definition);
        }

        return $inherited;
    }

    /**
     * Resolve recursive definitions
     *
     * @param mixed $definition
     * @return mixed
     */
    protected function resolveDefinition($definition) {
        // String
        if (is_string($definition)) {
            return isset($this->definitions[$definition]) ? $this->resolveDefinition($this->definitions[$definition]) : [static::CLASS_KEY => $definition];
        }

        // Array
        return isset($definition[static::CLASS_KEY]) ? $this->resolveArrayDefinition($definition) : $definition;
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
                throw new InvalidParameterException("Invalid abstract factory definition '$key'");
            }

            $this->abstractFactories[] = $abstractFactory;
        }
    }
}