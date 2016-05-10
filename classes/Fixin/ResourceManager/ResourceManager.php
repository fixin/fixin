<?php

namespace Fixin\ResourceManager;

use Closure;
use Fixin\Base\Exception\InvalidParameterException;
use Fixin\ResourceManager\AbstractFactory\AbstractFactoryInterface;
use Fixin\ResourceManager\Factory\FactoryInterface;

class ResourceManager implements ResourceManagerInterface {

    const ABSTRACT_FACTORIES = 'abstractFactories';
    const DEFINITIONS = 'definitions';

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
     * Adds abstract factory
     *
     * @param string|object $abstractFactory
     * @return \Fixin\ResourceManager\ResourceManager
     */
    public function addAbstractFactory($abstractFactory) {
        $this->setupAbstractFactories([$abstractFactory]);

        return $this;
    }

    /**
     * @param array $config
     * @return self
     */
    public function configure(array $config) {
        // Abstract factories
        if (isset($config[static::ABSTRACT_FACTORIES])) {
            $this->setupAbstractFactories($config[static::ABSTRACT_FACTORIES]);
        }

        // Definitions
        if ($values = $config[static::DEFINITIONS] ?? null) {
            $this->definitions = $values + $this->definitions;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\ResourceManager\ResourceManagerInterface::get($name)
     */
    public function get(string $name) {
        return $this->resources[$name] ?? $this->resources[$name] = $this->produceResource($name);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\ResourceManager\ResourceManagerInterface::has($name)
     */
    public function has(string $name): bool {
        // Resource or definition
        if (isset($this->resources[$name]) || isset($this->definitions[$name])) {
            return true;
        }

        // Abstract factories
        foreach ($this->abstractFactories as $abstractFactory) {
            if ($abstractFactory->canProduce($this, $name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Produces resource from definition or abstract factories
     *
     * @param string $name
     * @throws Exception\ResourceFaultException
     * @throws Exception\ResourceNotFoundException
     * @return mixed
     */
    protected function produceResource(string $name) {
        // Definitions
        if ($definition = $this->definitions[$name] ?? null) {
            // Resolve class name
            if (is_string($definition) && class_exists($definition)) {
                $this->definitions[$name] =
                $definition = new $definition();
            }

            // Factory
            if ($definition instanceof FactoryInterface) {
                return $definition->produce($this, $name);
            }

            // Non-Factory object
            if (is_object($definition) && !$definition instanceof Closure) {
                return $definition;
            }

            // Callable
            if (is_callable($definition)) {
                return $definition($this, $name);
            }

            throw new Exception\ResourceFaultException("Invalid definition registered for name '$name'");
        }

        // Abstract factories
        foreach ($this->abstractFactories as $abstractFactory) {
            if ($abstractFactory->canProduce($this, $name)) {
                return $abstractFactory->produce($this, $name);
            }
        }

        // Not found
        $similar = $this->unifiedResourceNames[strtolower($name)] ?? null;

        throw new Exception\ResourceNotFoundException("Resource is not registered with name '$name'" . ($similar ? ". Do you think '$similar'?" : ''));
    }

    /**
     * Sets definition
     *
     * @param string $name
     * @param unknown $definition
     * @throws Exception\OverrideNotAllowedException
     * @return self
     */
    public function setDefinition(string $name, $definition) {
        if (isset($this->resources[$name])) {
            throw new Exception\OverrideNotAllowedException("Definition name '$name' already used");
        }

        $this->definitions[$name] = $definition;

        return $this;
    }

    /**
     * Sets resource
     *
     * @param string $name
     * @param \stdClass $resource
     * @throws Exception\OverrideNotAllowedException
     * @return self
     */
    public function setResource(string $name, \stdClass $resource) {
        if (isset($this->resources[$name])) {
            throw new Exception\OverrideNotAllowedException("Resource name '$name' already used");
        }

        $this->resources[$name] = $resource;

        return $this;
    }

    /**
     * Sets abstract factories
     *
     * @param array $abstractFactories
     * @throws InvalidParameterException
     */
    protected function setupAbstractFactories(array $abstractFactories) {
        foreach ($abstractFactories as $abstractFactory) {
            // Resolve class name
            if (is_string($abstractFactory) && class_exists($abstractFactory)) {
                $abstractFactory = new $abstractFactory($this);
            }

            if ($abstractFactory instanceof AbstractFactoryInterface) {
                $this->abstractFactories[] = $abstractFactory;

                continue;
            }

            if (is_string($abstractFactory)) {
                throw new InvalidParameterException('Invalid abstract factory: ' . $abstractFactory);
            }

            throw new InvalidParameterException('Invalid type for abstract factory: ' . gettype($abstractFactory));
        }
    }
}