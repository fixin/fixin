<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Resource;

abstract class Managed implements ManagedInterface
{
    protected const
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

        OPTION_TYPE_CHECKS = [
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
        ],

        OPTION_TYPE_NAMES = [
            self::ANY_TYPE => 'any',
            self::ARRAY_TYPE => 'array',
            self::BOOL_TYPE => 'bool',
            self::CALLABLE_TYPE => 'callable',
            self::FLOAT_TYPE => 'float',
            self::INT_TYPE => 'int',
            self::NULL_TYPE => 'null',
            self::NUMERIC_TYPE => 'numeric',
            self::OBJECT_TYPE => 'object',
            self::SCALAR_TYPE => 'scalar',
            self::STRING_TYPE => 'string'
        ],

        THIS_REQUIRES = [],
        THIS_SETS = [],
        THIS_SETS_LAZY = [],

        INVALID_ARGUMENT_EXCEPTION = "Invalid '%s' argument: %s allowed",
        INVALID_OPTION_EXCEPTION = "Invalid option '%s'",
        INVALID_RESOURCE_EXCEPTION = "Invalid '%s' resource: %s allowed",
        REQUIRED_PROPERTY_EXCEPTION = "'%s' is required for %s";

    /**
     * @var array[]
     */
    private $lazyLoadingProperties = [];

    /**
     * @var ResourceManagerInterface
     */
    protected $resourceManager;

    public function __construct(ResourceManagerInterface $resourceManager, array $options = null, string $name = null)
    {
        $this->resourceManager = $resourceManager;

        // Options
        if (isset($options)) {
            $this->configureWithOptions($options);
        }
    }

    /**
     * @throws Exception\RuntimeException
     * @return $this
     */
    protected function configurationTest(string $name): self
    {
        foreach (static::THIS_REQUIRES as $key) {
            if (isset($this->$key)) {
                continue;
            }

            throw new Exception\RuntimeException(sprintf(static::REQUIRED_PROPERTY_EXCEPTION, $key, $name));
        }

        return $this;
    }

    /**
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    protected function configureWithOptions(array $options): self
    {
        foreach ($options as $name => $value) {
            // By setter
            if (method_exists($this, $method = 'set' . $name)) {
                $this->$method($value);

                continue;
            }

            // Normal property
            if (isset(static::THIS_SETS[$name])) {
                foreach ((array) static::THIS_SETS[$name] as $type) {
                    if (is_int($type)) {
                        $function = self::OPTION_TYPE_CHECKS[$type] ?? false;
                        if ($function === true || ($function && $function($value))) {
                            $this->$name = $value;

                            continue 2;
                        }
                    }
                    elseif ($value instanceof $type) {
                        $this->$name = $value;

                        continue 2;
                    }
                }

                $interface = implode(', ', array_map(function ($type) {
                    return is_int($type) ? (self::OPTION_TYPE_NAMES[$type] ?? $type) : $type;
                }, (array) static::THIS_SETS[$name]));

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

            return $this->$propertyName = is_subclass_of($interface, ResourceInterface::class) ? $this->resourceManager->get($set[0], $interface) : $this->resourceManager->clone($set[0], $interface, $prototypeOptions);
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
}
