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
    private const
        TYPE_CHECKS = [
            self::ANY_TYPE => true,
            self::ARRAY_TYPE => 'is_array',
            self::BOOL_TYPE => 'is_bool',
            self::CALLABLE_TYPE => 'is_callable',
            self::FLOAT_TYPE => 'is_float',
            self::INT_TYPE => 'is_int',
            self::NULL_TYPE => 'is_null',
            self::NUMERIC_TYPE => 'is_numeric',
            self::OBJECT_TYPE => 'is_object',
            self::SCALAR_TYPE => 'is_scalar',
            self::STRING_TYPE => 'is_string'
        ];

    protected const
        INVALID_ARGUMENT_EXCEPTION = "Invalid '%s' argument: %s allowed",
        INVALID_OPTION_EXCEPTION = "Invalid option '%s'",
        INVALID_RESOURCE_EXCEPTION = "Invalid '%s' resource: %s allowed",
        REQUIRED_PROPERTY_EXCEPTION = "'%s' is required for %s",

        ANY_TYPE = 1,
        ARRAY_TYPE = 2,
        BOOL_TYPE = 3,
        CALLABLE_TYPE = 4,
        FLOAT_TYPE = 5,
        INT_TYPE = 6,
        NULL_TYPE = 7,
        NUMERIC_TYPE = 8,
        OBJECT_TYPE = 9,
        SCALAR_TYPE = 10,
        STRING_TYPE = 11,

        THIS_REQUIRES = [],
        THIS_SETS = [],
        THIS_SETS_LAZY = [];

    /**
     * @var ResourceManagerInterface
     */
    protected $resourceManager;

    /**
     * @var array[]
     */
    private $lazyLoadingProperties = [];

    public function __construct(ResourceManagerInterface $resourceManager, array $options = null, string $name = null)
    {
        $this->resourceManager = $resourceManager;

        // Options
        if (isset($options)) {
            $this->configureWithOptions($options);
        }

        // Configuration test
        $this->configurationTest($name);
    }

    /**
     * @throws Exception\RuntimeException
     * @return $this
     */
    protected function configurationTest(string $name): Resource
    {
        foreach (static::THIS_REQUIRES as $key) {
            $value = $this->$key;

            // TODO: notNull / notEmpty?
            if ($value !== null && $value !== '' && $value !== []) {
                continue;
            }

            throw new Exception\RuntimeException(sprintf(static::REQUIRED_PROPERTY_EXCEPTION, $key, $name));
        }

        return $this;
    }

    /**
     * @throws Exception\InvalidArgumentException
     * @return $this
     */
    protected function configureWithOptions(array $options): Resource
    {
        foreach ($options as $name => $value) {
            // Setter for property
            if (method_exists($this, $method = 'set' . $name)) {
                $this->$method($value);

                continue;
            }

            // Normal-loading property
            if (isset(static::THIS_SETS[$name])) {
                if (is_null($value) || $value instanceof ($interface = static::THIS_SETS[$name])) {
                    $this->$name = $value;

                    continue;
                }

                $function = self::TYPE_CHECKS[$interface] ?? false;
                if ($function === true || ($function && $function($value))) {
                    $this->$name = $value;

                    continue;
                }

                throw new Exception\InvalidArgumentException(sprintf(static::INVALID_ARGUMENT_EXCEPTION, $name, $interface));
            }

            // Lazy-loading property
            if (isset(static::THIS_SETS_LAZY[$name])) {
                $this->setLazyLoadingProperty($name, static::THIS_SETS_LAZY[$name], $value);

                continue;
            }

            throw new Exception\InvalidArgumentException(sprintf(static::INVALID_OPTION_EXCEPTION, $name));
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
            $value = is_subclass_of($interface, PrototypeInterface::class) ? $this->resourceManager->clone($set[0], $prototypeOptions) : $this->resourceManager->get($set[0]);

            if ($value instanceof $interface) {
                return $this->$propertyName = $value;
            }

            throw new Exception\InvalidArgumentException(sprintf(static::INVALID_RESOURCE_EXCEPTION, $propertyName, $interface));
        }

        return $this->$propertyName = null;
    }

    /**
     * @throws Exception\InvalidArgumentException
     */
    protected function setLazyLoadingProperty(string $propertyName, string $interface, $value): void
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

        throw new Exception\InvalidArgumentException(sprintf(static::INVALID_ARGUMENT_EXCEPTION, $propertyName, 'string or ' . $interface));
    }

    /**
     * @return static
     */
    public function withResourceManager(ResourceManagerInterface $resourceManager): ResourceInterface
    {
        $clone = clone $this;
        $clone->resourceManager = $resourceManager;

        return $clone;
    }
}
