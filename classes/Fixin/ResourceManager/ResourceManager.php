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

class ResourceManager implements ResourceManagerInterface {

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
     * @param array $config
     */
    public function __construct(array $config = []) {
        $this->configure($config);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\ResourceManager\ResourceManagerInterface::clonePrototype()
     */
    public function clonePrototype(string $name) {
        return clone $this->getResource($name, true);
    }

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
        if (class_exists($class = $definition[static::CLASS_KEY] ?? '')) {
            return new $class($this, $definition[static::OPTIONS_KEY]);
        }

        return null;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Support\ContainerInterface::get($name)
     */
    public function get(string $name) {
        return $this->getResource($name, false);
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
     * {@inheritDoc}
     * @see \Fixin\Support\ContainerInterface::has($name)
     */
    public function has(string $name): bool {
        // Made or defined
        if (isset($this->definitions[$name])) {
            return true;
        }

        // Abstract factories
        $has = false;

        foreach ($this->abstractFactories as $abstractFactory) {
            if ($abstractFactory->canProduce($this, $name)) {
                $has = true;
                break;
            }
        }

        return $has;
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
        $definition = $this->resolveDefinition($name);
        $resource = $definition;
        $options = $definition[static::OPTIONS_KEY] ?? [];

        // Definition
        if (is_array($definition)) {
            $resource = $this->createFromDefinition($definition);

            // Handle by abstract factories
            if (!$resource) {
                $class = $definition[static::CLASS_KEY];

                foreach ($this->abstractFactories as $abstractFactory) {
                    if ($abstractFactory->canProduce($this, $class)) {
                        $resource = $abstractFactory($this, $options, $class);
                    }
                }
            }
        }

        // Factory or Closure
        if ($resource instanceof FactoryInterface || $resource instanceof \Closure) {
            $resource = $resource($this, $options, $name);
        }

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
        if (isset($definition[static::CLASS_KEY])) {
            $inherited = $this->resolveDefinition($definition[static::CLASS_KEY]);

            if (is_array($inherited)) {
                unset($definition[static::CLASS_KEY]);

                return array_replace_recursive($inherited, $definition);
            }

            return $inherited;
        }

        return $definition;
    }

    /**
     * Set resource
     *
     * @param string $name
     * @param object $resource
     * @throws InvalidParameterException
     * @return self
     */
    public function setResource(string $name, $resource) {
        if (!is_object($resource)) {
            throw new InvalidParameterException('Resource must be an object.');
        }

        $this->configure([static::RESOURCES_KEY => [$name => $resource]]);
        $this->definitions[$name] = true;

        return $this;
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