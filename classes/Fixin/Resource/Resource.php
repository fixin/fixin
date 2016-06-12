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

    const CONFIGURATION_REQUIRES = [];
    const EXCEPTION_INVALID_OPTION = "Invalid option name '%s'";
    const EXCEPTION_REQUIRED_NOT_SET = "'%s' not set";

    /**
     * @var string[]
     */
    protected $configurationRequires = [];

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
     */
    private function configurationArrayTest($value): bool {
        return is_array($value) && count($value);
    }

    /**
     * Instance test
     *
     * @param mixed $value
     * @return bool
     */
    private function configurationInstanceTest($value): bool {
        return $value === false || is_object($value);
    }

    /**
     * String test
     *
     * @param mixed $value
     * @return bool
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
        foreach (static::CONFIGURATION_REQUIRES as $key => $type) {
            $passed = false;

            switch ($type) {
                case 'array':
                    $passed = $this->configurationArrayTest($this->$key);
                    break;

                case 'instance':
                    $passed = $this->configurationInstanceTest($this->$key);
                    break;

                case 'string':
                    $passed = $this->configurationStringTest($this->$key);
                    break;
            }

            if (!$passed) {
                throw new RuntimeException(sprintf(static::EXCEPTION_REQUIRED_NOT_SET, $key));
            }
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

            if (method_exists($this, $method)) {
                $this->$method($value);

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
     * @throws InvalidArgumentException
     * @return mixed
     */
    protected function loadLazyProperty(string $propertyName) {
        if (isset($this->lazyLoadingProperties[$propertyName])) {
            $set = $this->lazyLoadingProperties[$propertyName];
            $interface = $set[1];
            $value = $interface instanceof PrototypeInterface ? $this->container->clonePrototype($set[0]) : $this->container->get($set[0]);

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