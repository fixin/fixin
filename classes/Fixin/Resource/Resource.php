<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Resource;

abstract class Resource implements ResourceInterface
{
    protected const
        EXCEPTION_INVALID_ARGUMENT = "Invalid '%s' argument: %s allowed",
        EXCEPTION_INVALID_OPTION = "Invalid option name '%s'",
        EXCEPTION_INVALID_RESOURCE = "Invalid '%s' resource: %s allowed",
        EXCEPTION_CONFIGURATION_REQUIRES = "'%s' is a requried %s for %s",
        THIS_REQUIRES = [],
        THIS_SETS_LAZY = [],
        TYPE_ANY = 'any',
        TYPE_ARRAY = 'array',
        TYPE_BOOL = 'bool',
        TYPE_INSTANCE = 'instance',
        TYPE_STRING = 'string';

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
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function configurationAnyTest($value): bool
    {
        return isset($value);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function configurationArrayTest($value): bool
    {
        return is_array($value) && count($value);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function configurationBoolTest($value): bool
    {
        return is_bool($value);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function configurationInstanceTest($value): bool
    {
        return $value === false || is_object($value);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function configurationStringTest($value): bool
    {
        return is_string($value) && $value !== '';
    }

    /**
     * @throws Exception\RuntimeException
     * @return static
     */
    protected function configurationTests(): Resource
    {
        foreach (static::THIS_REQUIRES as $key => $type) {
            if ($this->{"configuration{$type}Test"}($this->$key)) {
                continue;
            }

            throw new Exception\RuntimeException(sprintf(static::EXCEPTION_CONFIGURATION_REQUIRES, $key, $type, get_class($this)));
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
            $value = is_subclass_of($interface, PrototypeInterface::class) ? $this->container->clonePrototype($set[0], $prototypeOptions) : $this->container->get($set[0]);

            if ($value instanceof $interface) {
                return $this->$propertyName = $value;
            }

            throw new Exception\InvalidArgumentException(sprintf(static::EXCEPTION_INVALID_RESOURCE, $propertyName, $interface));
        }

        return $this->$propertyName = null;
    }

    /**
     * @throws Exception\InvalidArgumentException
     * @return static
     */
    protected function setLazyLoadingProperty(string $propertyName, string $interface, $value): Resource
    {
        // Key
        if (is_string($value)) {
            $this->{$propertyName} = false;
            $this->lazyLoadingProperties[$propertyName] = [$value, $interface];

            return $this;
        }

        // Instance
        if ($value instanceof $interface) {
            $this->{$propertyName} = $value;
            unset($this->lazyLoadingProperties[$propertyName]);

            return $this;
        }

        throw new Exception\InvalidArgumentException(sprintf(static::EXCEPTION_INVALID_ARGUMENT, $propertyName, 'string or ' . $interface));
    }
}
