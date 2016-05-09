<?php

namespace Fixin\ResourceManager;

use Fixin\Base\WithOptions;

class ResourceManager extends WithOptions implements ServiceLocatorInterface {

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
     * Unified names
     *
     * @var array
     */
    protected $unifiedNames = [];

    public function canCreateByAbstractFactory(string $key, string $name): bool {
        return false;
    }

    public function create(string $key, string $name) {
        $instance = null;

        // Circular dependency
        if (isset($this->dependencyStack[$key])) {
            $this->dependencyStack = [];
            throw new Exception\CircularDependencyException('Circular dependency found for ' . $name);
        }

        try {
            $this->dependencyStack[$key] = true;

            // Definition
            if (isset($this->definitions[$key])) {
                $instance = $this->createFromDefinition($key, $name);
            }

            // Abstract factories
            if (!$instance && $this->canCreateByAbstractFactory($key, $name)) {
                $instance = $this->createFromAbstractFactory($key, $name);
            }

            // Circular dependency
            unset($this->dependencyStack[$key]);
        }
        catch (\Exception $e) {
            unset($this->dependencyStack[$key]);
            throw $e;
        }

        // Can't create
        if (!$instance) {
            throw new Exception\ResourceFaultException('Resource not allocatable for ' . $name);
        }

        return $instance;
    }

    /**
     * Create instanse from definition
     *
     * @param string $key
     * @param string $name
     * @throws Exception\ResourceFaultException
     * @return object
     */
    protected function createFromDefinition(string $key, string $name) {
        $creator = $this->definitions[$key];

        // Resolve class string
        if (is_string($creator) && class_exists($creator)) {
            $creator = new $creator();
            $this->definitions[$key] = $creator;
        }

        // Factory
        if ($creator instanceof FactoryInterface) {
            return $creator->createSource();
        }
        elseif (is_callable($creator)) {
            return $creator();
        }
        elseif (is_object($creator)) {
            return $creator;
        }

        throw new Exception\ResourceFaultException('Invalid factory registered: ' . $name);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\ResourceManager\ServiceLocatorInterface::get()
     */
    public function get(string $name) {
        $key = $this->unifiedNames[$name] ?? $this->unifieName($name);

        // Resource
        if (isset($this->resources[$key])) {
            return $this->resources[$key];
        }

        // Create
        $instance = $this->create($key, $name);

        return $instance;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\ResourceManager\ServiceLocatorInterface::has()
     */
    public function has(string $name, bool $testAbstractCreation = true): bool {
        return $this->hasKey($this->unifiedNames[$name] ?? $this->unifieName($name), $testAbstractCreation);
    }

    /**
     * Checks key
     *
     * @param string $key
     * @param bool $testAbstractCreation
     * @return bool
     */
    protected function hasKey(string $key, bool $testAbstractCreation): bool {
        return isset($this->resources[$key]) || isset($this->factories[$key]) || ($testAbstractCreation && $this->canCreateByAbstractFactory($key));
    }

    public function set(string $name, \stdClass $resource) {
        $key = $this->unifiedNames[$name] ?? $this->unifieName($name);

        if ($this->hasKey($key, false)) {
            throw new Exception\InvalidResourceNameException(sprintf('A resource with the name "%s" already exists.', $name));
        }

        $this->resources[$key] = $resource;

        return $this;
    }

    /**
     * Sets definition
     *
     * @param string $name
     * @param string|FactoryInterface|callable $definition
     * @return \Fixin\ResourceManager\ResourceManager
     */
    public function setDefinition(string $name, $definition) {
        $key = $this->unifiedNames[$name] ?? $this->unifieName($name);

        // Test
        if (!is_object($definition) && !is_callable($definition)) {

        }

        // Already existing
        if ($this->hasKey($key, false)) {
            throw new Exception\InvalidResourceNameException(sprintf('A resource with the name "%s" already exists.', $name));
        }

        // Store
        $this->definitions[$key] = $definition;

        return $this;
    }

    /**
     * Sets multiple definitions
     *
     * @param array $definitions
     * @return \Fixin\ResourceManager\ResourceManager
     */
    public function setDefinitions(array $definitions) {
        foreach ($definitions as $name => $definition) {
            $this->setDefinition($name, $definition);
        }

        return $this;
    }

    /**
     * Unifie name
     *
     * @param string $name
     * @return string
     */
    protected function unifieName(string $name) {
        return $this->unifiedNames[$name] = strtolower($name);
    }
}