<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Resource;

use Fixin\Base\Exception\InvalidArgumentException;

abstract class Resource implements ResourceInterface {

    const EXCEPTION_INVALID_OPTION = "Invalid option name '%s'";
    const EXCEPTION_PROPERTY_NOT_SET = "'%s' not set";

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
     * Configuration test
     *
     * @return self
     */
    protected function configurationTests() {
        return $this;
    }

    /**
     * Configure
     *
     * @param array $options
     * @throws InvalidArgumentException
     */
    protected function configureWithOptions(array $options) {
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