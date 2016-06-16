<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Resource;

use Fixin\Exception\InvalidArgumentException;
use Fixin\Exception\RuntimeException;

abstract class Resource implements ResourceInterface {

    const EXCEPTION_INVALID_OPTION = "Invalid option name '%s'";
    const EXCEPTION_CONFIGURATION_REQUIRES = "'%s' is a requried %s for %s";
    const THIS_REQUIRES = [];
    const THIS_SETS_LAZY = [];
    const TYPE_ARRAY = 'array';
    const TYPE_INSTANCE = 'instance';
    const TYPE_STRING = 'string';

    /**
     * @var ResourceManagerInterface
     */
    protected $container;

    /**
     * @var array[]
     */
    private $lazyLoadingProperties = [];

    /**
     * @param ResourceManagerInterface $container
     * @param array $options
     * @param string $name
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(ResourceManagerInterface $container, array $options = null, string $name = null) {
        $this->container = $container;

        // Options
        if (isset($options)) {
            $this->configureWithOptions($options);
        }

        // Configuration test
        $this->configurationTests();
    }

    /**
     * Array test
     *
     * @param mixed $value
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function configurationArrayTest($value): bool {
        return is_array($value) && count($value);
    }

    /**
     * Instance test
     *
     * @param mixed $value
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function configurationInstanceTest($value): bool {
        return $value === false || is_object($value);
    }

    /**
     * String test
     *
     * @param mixed $value
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function configurationStringTest($value): bool {
        return is_string($value) && $value !== '';
    }

    /**
     * Configuration tests
     *
     * @return self
     */
    protected function configurationTests(): Resource {
        foreach (static::THIS_REQUIRES as $key => $type) {
            if ($this->{"configuration{$type}Test"}($this->$key)) {
                continue;
            }

            throw new RuntimeException(sprintf(static::EXCEPTION_CONFIGURATION_REQUIRES, $key, $type, get_class($this)));
        }

        return $this;
    }

    /**
     * Configure
     *
     * @param array $options
     * @throws InvalidArgumentException
     */
    protected function configureWithOptions(array $options): Resource {
        foreach ($options as $key => $value) {
            $method = 'set' . $key;

            // Setter for property
            if (method_exists($this, $method)) {
                $this->$method($value);

                continue;
            }

            // Lazy-loading property
            if (isset(static::THIS_SETS_LAZY[$key])) {
                $this->setLazyLoadingProperty($key, static::THIS_SETS_LAZY[$key], $value);

                continue;
            }

            throw new InvalidArgumentException(sprintf(static::EXCEPTION_INVALID_OPTION, $key));
        }

        return $this;
    }

    /**
     * Load lazy property
     *
     * @param string $propertyName
     * @param array $prototypeOptions
     * @throws InvalidArgumentException
     * @return mixed
     */
    protected function loadLazyProperty(string $propertyName, array $prototypeOptions = []) {
        if (isset($this->lazyLoadingProperties[$propertyName])) {
            $set = $this->lazyLoadingProperties[$propertyName];
            $interface = $set[1];
            $value = is_subclass_of($interface, PrototypeInterface::class) ? $this->container->clonePrototype($set[0], $prototypeOptions) : $this->container->get($set[0]);

            if ($value instanceof $interface) {
                return $this->$propertyName = $value;
            }

            throw new InvalidArgumentException(sprintf(InvalidArgumentException::INVALID_RESOURCE, $propertyName, $interface));
        }

        return $this->$propertyName = null;
    }

    /**
     * Set lazy-loading property
     *
     * @param string $propertyName
     * @param string $interface
     * @param string|object $value
     * @throws InvalidArgumentException
     */
    protected function setLazyLoadingProperty(string $propertyName, string $interface, $value) {
        // Key
        if (is_string($value)) {
            $this->{$propertyName} = false;
            $this->lazyLoadingProperties[$propertyName] = [$value, $interface];

            return;
        }

        // Instance
        if ($value instanceof $interface) {
            $this->{$propertyName} = $value;
            unset($this->lazyLoadingProperties[$propertyName]);

            return;
        }

        throw new InvalidArgumentException(sprintf(InvalidArgumentException::INVALID_ARGUMENT, $propertyName, 'string or ' . $interface));
    }
}