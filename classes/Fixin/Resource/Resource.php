<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Resource;

abstract class Resource implements ResourceInterface
{
    protected const
        EXCEPTION_INVALID_ARGUMENT = "Invalid '%s' argument: %s allowed",
        EXCEPTION_INVALID_OPTION = "Invalid option name '%s'",
        EXCEPTION_INVALID_RESOURCE = "Invalid '%s' resource: %s allowed",
        EXCEPTION_CONFIGURATION_REQUIRES = "'%s' is required for %s",
        THIS_REQUIRES = [],
        THIS_SETS_LAZY = [];

    /**
     * @var ResourceManagerInterface
     */
    protected $container;

    /**
     * @var array[]
     */
    private $lazyLoadingProperties = [];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(ResourceManagerInterface $container, array $options = null, string $name = null)
    {
        $this->container = $container;

        // Options
        if (isset($options)) {
            $this->configureWithOptions($options);
        }

        // Configuration test
        $this->configurationTests();
    }

    /**
     * @throws Exception\RuntimeException
     * @return static
     */
    protected function configurationTests(): Resource
    {
        foreach (static::THIS_REQUIRES as $key) {
            $value = $this->{$key};
            if ($value !== null && $value !== '' && $value !== []) {
                continue;
            }

            throw new Exception\RuntimeException(sprintf(static::EXCEPTION_CONFIGURATION_REQUIRES, $key, get_class($this)));
        }

        return $this;
    }

    /**
     * @throws Exception\InvalidArgumentException
     * @return static
     */
    protected function configureWithOptions(array $options): Resource
    {
        foreach ($options as $key => $value) {
            // Setter for property
            if (method_exists($this, $method = 'set' . $key)) {
                $this->$method($value);

                continue;
            }

            // Lazy-loading property
            if (isset(static::THIS_SETS_LAZY[$key])) {
                $this->setLazyLoadingProperty($key, static::THIS_SETS_LAZY[$key], $value);

                continue;
            }

            throw new Exception\InvalidArgumentException(sprintf(static::EXCEPTION_INVALID_OPTION, $key));
        }

        return $this;
    }

    /**
     * @throws Exception\InvalidArgumentException
     */
    protected function loadLazyProperty(string $propertyName, array $prototypeOptions = [])
    {
        if (isset($this->lazyLoadingProperties[$propertyName])) {
            $set = $this->lazyLoadingProperties[$propertyName];
            $interface = $set[1];
            $value = is_subclass_of($interface, PrototypeInterface::class) ? $this->container->clone($set[0], $prototypeOptions) : $this->container->get($set[0]);

            if ($value instanceof $interface) {
                return $this->$propertyName = $value;
            }

            throw new Exception\InvalidArgumentException(sprintf(static::EXCEPTION_INVALID_RESOURCE, $propertyName, $interface));
        }

        return $this->$propertyName = null;
    }

    /**
     * @throws Exception\InvalidArgumentException
     */
    private function setLazyLoadingProperty(string $propertyName, string $interface, $value): void
    {
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

        throw new Exception\InvalidArgumentException(sprintf(static::EXCEPTION_INVALID_ARGUMENT, $propertyName, 'string or ' . $interface));
    }
}
