<?php

namespace Fixin\ResourceManager;

use Closure;
use Fixin\Base\Configurable\ConfigurableInterface;
use Fixin\Base\Exception\InvalidParameterException;
use Fixin\ResourceManager\AbstractFactory\AbstractFactoryInterface;
use Fixin\ResourceManager\Factory\FactoryInterface;
use Fixin\Support\ContainerInterface;

class ResourceManager implements ContainerInterface, ConfigurableInterface {

    const ABSTRACT_FACTORIES_KEY = 'abstractFactories';
    const CLASS_KEY = 'class';
    const DEFINITIONS_KEY = 'definitions';
    const RESOURCES_KEY = 'resources';

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
     * @return self
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
        if (isset($config[static::ABSTRACT_FACTORIES_KEY])) {
            $this->setupAbstractFactories($config[static::ABSTRACT_FACTORIES_KEY]);
        }

        // Definitions
        if ($values = $config[static::DEFINITIONS_KEY] ?? null) {
            if ($names = array_intersect_key($values, $this->definitions)) {
                throw new Exception\OverrideNotAllowedException("Definition already defined for '" . implode("', '", array_keys($names)) . "'");
            }

            $this->definitions = $values + $this->definitions;
        }

        // Resources
        if ($values = $config[static::RESOURCES_KEY] ?? null) {
            if ($names = array_intersect_key($values, $this->resources)) {
                throw new Exception\OverrideNotAllowedException("Resource already defined for '" . implode("', '", array_keys($names)) . "'");
            }

            $this->resources = $values + $this->resources;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Support\ContainerInterface::get($name)
     */
    public function get(string $name) {
        return $this->resources[$name] ?? $this->resources[$name] = $this->produceResource($name);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Support\ContainerInterface::has($name)
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
                $definition = new $definition($this);
            }
            // Resolve class array
            elseif (is_array($definition) && ($class = $definition[static::CLASS_KEY] ?? null) && class_exists($class)) {
                unset($definition[static::CLASS_KEY]);

                $definition = new $class($this, $definition);
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
        throw new Exception\ResourceNotFoundException("Resource is not registered with name '$name'");
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
        $this->configure([static::DEFINITIONS_KEY => [$name => $definition]]);

        return $this;
    }

    /**
     * Sets resource
     *
     * @param string $name
     * @param object $resource
     * @throws Exception\OverrideNotAllowedException
     * @return self
     */
    public function setResource(string $name, $resource) {
        if (!is_object($resource)) {
            throw new InvalidParameterException('Resource must be an object.');
        }

        $this->configure([static::RESOURCES_KEY => [$name => $resource]]);

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
            // Resolve class array
            elseif (is_array($abstractFactory) && ($class = $abstractFactory[static::CLASS_KEY] ?? null) && class_exists($class)) {
                unset($abstractFactory[static::CLASS_KEY]);

                $abstractFactory = new $class($this, $abstractFactory);
            }

            if ($abstractFactory instanceof AbstractFactoryInterface) {
                $this->abstractFactories[] = $abstractFactory;

                continue;
            }

            // Fault
            if (is_string($abstractFactory)) {
                throw new InvalidParameterException('Invalid abstract factory: ' . $abstractFactory);
            }

            if (is_array($abstractFactory)) {
                throw new InvalidParameterException('Invalid abstract factory array data');
            }

            throw new InvalidParameterException('Invalid type for abstract factory: ' . gettype($abstractFactory));
        }
    }
}