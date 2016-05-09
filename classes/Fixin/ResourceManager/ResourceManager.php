<?php

namespace Fixin\ResourceManager;

use Closure;
use Fixin\Base\Configurable;
use Fixin\ResourceManager\Factory\FactoryInterface;
use Fixin\ResourceManager\AbstractFactory\AbstractFactoryInterface;

class ResourceManager extends Configurable implements ResourceManagerInterface {

    const ABSTRACT_FACTORIES_KEY = 'abstractFactories';

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
     * Under resolve
     *
     * @var array
     */
    protected $dependencyStack = [];

    /**
     * Allocated resources
     *
     * @var array
     */
    protected $resources = [];

    /**
     * Unified resource names
     *
     * @var array
     */
    protected $unifiedResourceNames = [];

    /**
     * @param array $config
     */
    public function __construct(array $config = []) {
        // Abstract factories
        if (isset($config[static::ABSTRACT_FACTORIES_KEY])) {
            foreach ($config[static::ABSTRACT_FACTORIES_KEY] as $abstractFactory) {
                $this->appendAbstractFactory($abstractFactory);
            }

            unset($config[static::ABSTRACT_FACTORIES_KEY]);
        }

        $this->iHaveToWriteThis = 'iHaveToWriteThis';

        parent::__construct($config);
    }

    /**
     * Appends abstract factory to the list
     *
     * @param AbstractFactoryInterface|string $factory
     * @return \Fixin\ResourceManager\ResourceManager
     */
    public function appendAbstractFactory($factory) {
        array_push($this->abstractFactories, $this->createAbstractFactory($factory));

        return $this;
    }

    protected function canCreateResourceFromAbstractFactory(string $name) {
        return false;
    }

    protected function create(string $name) {
        // Circular dependency test
        if (isset($this->dependencyStack[$name])) {
            $this->dependencyStack = [];
            throw new Exception\CircularDependencyException('Circular dependency found for ' . $name);
        }

        // Indicate no source
        $instance = false;

        // Create
        try {
            // Circular dependency
            $this->dependencyStack[$name] = true;

            // Definition
            if (isset($this->definitions[$name])) {
                $instance = $this->createResourceFromDefinition($name);
            }

            // Abstract factories
            if (!$instance && $this->canCreateResourceFromAbstractFactory($name)) {
                $instance = $this->createFromAbstractFactory($name);
            }

            // Circular dependency
            unset($this->dependencyStack[$name]);
        }
        catch (\Exception $e) {
            unset($this->dependencyStack[$name]);
            throw $e;
        }

        // Faults
        if ($instance === null) {
            throw new Exception\ResourceFaultException('Resource not allocatable for name "' . $name . '"');
        }
        elseif ($instance === false) {
            $similar = $this->unifiedResourceNames[strtolower($name)] ?? null;

            throw new Exception\ResourceNotFoundException('Resource is not registered with name "' . $name . '"' . ($similar ? '. Do you think "' . $similar . '"?' : ''));
        }

        return $instance;
    }

    protected function createAbstractFactory($factory) {
        // Resolve class name
        if (is_string($factory) && class_exists($factory)) {
            $factory = new $factory($this);
        }

        if (!$factory instanceof AbstractFactoryInterface) {
            throw new InvalidArgumentException('Invalid type for abstract factory: ' . gettype($definition));
        }

        return $factory;
    }

    protected function createResourceFromDefinition(string $name) {
        $creator = $this->definitions[$name];

        // Resolve class name
        if (is_string($creator) && class_exists($creator)) {
            $this->definitions[$name] =
            $creator = new $creator($this);
        }

        // Factory
        if ($creator instanceof FactoryInterface) {
            return $creator->produce($this);
        }
        // Object
        elseif (is_object($creator) && !$creator instanceof Closure) {
            return $creator;
        }
        // Callable
        elseif (is_callable($creator)) {
            return $creator($this);
        }

        throw new Exception\ResourceFaultException('Invalid definition registered for name "' . $name . '"');
    }

    public function get(string $name) {
        return $this->resources[$name] ?? $this->resources[$name] = $this->create($name);
    }

    public function has(string $name, bool $testAbstractCreation = true): bool {
        return isset($this->resources[$name]) || isset($this->definitions[$name]) || ($testAbstractCreation && $this->canCreateFromAbstractFactory($name));
    }

    /**
     * Prepends abstract factory to the list
     *
     * @param AbstractFactoryInterface|string $factory
     * @return \Fixin\ResourceManager\ResourceManager
     */
    public function prependAbstractFactory($factory) {
        array_unshift($this->abstractFactories, $this->createAbstractFactory($factory));

        return $this;
    }

    /**
     * Registers resource definition
     *
     * @param string $name
     * @param string|object|callable $definition
     * @throws Exception\InvalidResourceNameException
     * @throws InvalidConfigurationException
     * @return self
     */
    public function set(string $name, $definition) {
        $unifiedName = strtolower($name);

        // Already exists
        if ($existingName = $this->unifiedResourceNames[$unifiedName] ?? null) {
            throw new Exception\InvalidResourceNameException('A resource with a corresponding name "' . $existingName . '" already registered');
        }

        // Type check
        if (is_object($definition) || is_callable($definition, true)) {
            $this->definitions[$name] = $definition;
            $this->unifiedResourceNames[$unifiedName] = $name;
        }
        else {
            throw new InvalidConfigurationException('Invalid configuration type for the name "' . $name . '": ' . gettype($definition));
        }

        return $this;
    }

    /**
     * Registers multiple resource definitions
     *
     * @param array $definitions
     * @return self
     */
    public function setDefinitions(array $definitions) {
        foreach ($definitions as $name => $definition) {
            $this->set($name, $definition);
        }

        return $this;
    }
}