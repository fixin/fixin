<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\ResourceManager;

use Fixin\Base\Exception\InvalidArgumentException;
use Fixin\Base\Exception\RuntimeException;
use Fixin\ResourceManager\AbstractFactory\AbstractFactoryInterface;
use Fixin\ResourceManager\Factory\FactoryInterface;
use Fixin\Support\PrototypeInterface;

abstract class ResourceManagerBase implements ResourceManagerInterface {

    const EXCEPTION_ALREADY_DEFINED = "%s already defined for '%'";
    const EXCEPTION_CLASS_NOT_FOUND_FOR = "Class not found for '%s'";
    const EXCEPTION_GET_ERRORS = [
        "Resource not accessible by name '%s'",
        "Prototype not accessible by name '%s'",
        "Can't access prototype as normal resource '%s'",
        "Can't access normal resource as prototype '%s'",
    ];
    const EXCEPTION_INVALID_ABSTRACT_FACTORY_DEFINITION = "Invalid abstract factory definition '%s'";
    const EXCEPTION_INVALID_DEFINITION = "Invalid definition registered for name '%s'";

    const KEY_ABSTRACT_FACTORIES = 'abstractFactories';
    const KEY_CLASS = 'class';
    const KEY_DEFINITIONS = 'definitions';
    const KEY_OPTIONS = 'options';
    const KEY_RESOLVED = 'resolved';
    const KEY_RESOURCES = 'resources';

    const OPTIONS_INJECT_KEYS = [self::KEY_DEFINITIONS => 'Definition', self::KEY_RESOURCES => 'Resource'];

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
     * @param array $options
     */
    public function __construct(array $options) {
        // Abstract factories
        if (isset($options[static::KEY_ABSTRACT_FACTORIES])) {
            $this->setupAbstractFactories($options[static::KEY_ABSTRACT_FACTORIES]);
        }

        // Inject options
        foreach (static::OPTIONS_INJECT_KEYS as $key => $label) {
            if (isset($options[$key])) {
                $this->$key = $options[$key];
            }
        }
    }

    /**
     * Create object from definition
     *
     * @param string $name
     * @param array $definition
     * @return object|null
     */
    protected function createFromDefinition(string $name, array $definition) {
        if (class_exists($class = $definition[static::KEY_CLASS])) {
            return new $class($this, $definition[static::KEY_OPTIONS], $name);
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
     * Produce resource
     *
     * @param string $name
     * @throws Exception\ResourceFaultException
     * @return object
     */
    protected function produceResource(string $name) {
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
     * Produce resource from abstract factories
     *
     * @param string $name
     * @param array $options
     * @return mixed|NULL
     */
    protected function produceResourceFromAbstractFactories(string $name, array $options = null) {
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
        $class = $definition[static::KEY_CLASS];

        if (is_string($class)) {
            $class = $this->createFromDefinition($name, $definition) ?? $this->produceResourceFromAbstractFactories($class, $definition[static::KEY_OPTIONS]);
        }

        // Factory or Closure
        if ($class instanceof FactoryInterface || $class instanceof \Closure) {
            return $class($this, $definition[static::KEY_OPTIONS], $name);
        }

        return $class;
    }

    /**
     * Resolve definition (various data)
     *
     * @param mixed $definition
     * @return array
     */
    protected function resolveDefinition($definition): array {
        // String
        if (is_string($definition)) {
            return $this->resolveDefinitionFromName($definition);
        }
        // Array
        elseif (is_array($definition) && isset($definition[static::KEY_CLASS])) {
            $inherited = $this->resolveDefinitionFromName($definition[static::KEY_CLASS]);
            unset($definition[static::KEY_CLASS]);

            return array_replace_recursive($inherited, $definition);
        }

        return [
            static::KEY_CLASS => $definition,
            static::KEY_OPTIONS => null,
            static::KEY_RESOLVED => true
        ];
    }

    /**
     * Resolve definition from name
     *
     * @param string $name
     * @return array
     */
    protected function resolveDefinitionFromName(string $name): array {
        if (isset($this->definitions[$name])) {
            $definition = $this->definitions[$name];

            if (is_array($definition) && isset($definition[static::KEY_RESOLVED])) {
                return $definition;
            }

            return $this->definitions[$name] = $this->resolveDefinition($definition);
        }

        return [
            static::KEY_CLASS => $name,
            static::KEY_OPTIONS => null,
            static::KEY_RESOLVED => true
        ];
    }

    /**
     * Set abstract factories
     *
     * @param array $abstractFactories
     * @throws InvalidArgumentException
     */
    protected function setupAbstractFactories(array $abstractFactories) {
        foreach ($abstractFactories as $key => $abstractFactory) {
            $abstractFactory = $this->createFromDefinition($key, $this->resolveDefinition($abstractFactory));

            if (!$abstractFactory instanceof AbstractFactoryInterface) {
                throw new InvalidArgumentException(sprintf(static::EXCEPTION_INVALID_ABSTRACT_FACTORY_DEFINITION, $key));
            }

            $this->abstractFactories[] = $abstractFactory;
        }
    }
}