<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Resource;

use Fixin\Support\Types;

abstract class Managed
{
    protected const
        INVALID_ARGUMENT_EXCEPTION = "Invalid '%s' argument: %s allowed",
        LAZY_LOADING = -2,
        OPTION_TYPE_CHECKS = Types::CHECK_FUNCTIONS,
        OPTION_TYPE_NAMES = Types::NAME_LIST,
        REQUIRED_PROPERTY_EXCEPTION = "'%s' is required",
        THIS_SETS = [],
        UNKNOWN_ARGUMENT_EXCEPTION = "Unknown '%s' argument",
        USING_SETTER = -1;

    /**
     * @var array[]
     */
    private $lazyLoadingProperties = [];

    /**
     * @var ResourceManagerInterface
     */
    protected $resourceManager;

    /**
     * Managed constructor.
     *
     * @param ResourceManagerInterface $resourceManager
     * @param array $options
     */
    public function __construct(ResourceManagerInterface $resourceManager, array $options)
    {
        $this->resourceManager = $resourceManager;

        $this->configureWithOptions($options);
    }

    /**
     * Configure with options
     *
     * @param array $options
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    protected function configureWithOptions(array $options): self
    {
        foreach ($options as $name => $value) {
            if (isset(static::THIS_SETS[$name])) {
                $types = (array) static::THIS_SETS[$name];

                // Lazy-loading
                if (isset($value) && isset($types[static::LAZY_LOADING])) {
                    $this->setLazyLoadingProperty($name, $types[static::LAZY_LOADING], $value);

                    continue;
                }

                foreach ($types as $type) {
                    // Setter
                    if ($type === self::USING_SETTER) {
                        $this->{"set$name"}($value);

                        continue 2;
                    }

                    // Common types
                    if (is_int($type)) {
                        /** @var callable|bool $function */
                        $function = self::OPTION_TYPE_CHECKS[$type] ?? false;

                        if ($function === true || ($function && $function($value))) {
                            $this->$name = $value;

                            continue 2;
                        }
                    }

                    // Class
                    elseif ($value instanceof $type) {
                        $this->$name = $value;

                        continue 2;
                    }
                }

                $interface = implode(', ', array_map(function ($type) {
                    return is_int($type) ? (self::OPTION_TYPE_NAMES[$type] ?? $type) : $type;
                }, $types));

                throw new Exception\InvalidArgumentException(sprintf(static::INVALID_ARGUMENT_EXCEPTION, $name, $interface));
            }

            throw new Exception\InvalidArgumentException(sprintf(static::UNKNOWN_ARGUMENT_EXCEPTION, $name));
        }

        return $this;
    }

    /**
     * Load lazy property
     *
     * @param string $propertyName
     * @param array $prototypeOptions
     * @return mixed
     */
    protected function loadLazyProperty(string $propertyName, array $prototypeOptions = [])
    {
        if (isset($this->lazyLoadingProperties[$propertyName])) {
            $set = $this->lazyLoadingProperties[$propertyName];
            $interface = $set[1];

            // TODO ellenorizni hogy Prototype-e ha Resource; instanceof?
            return $this->$propertyName = is_subclass_of($interface, ResourceInterface::class) ? $this->resourceManager->get($set[0], $interface) : $this->resourceManager->clone($set[0], $interface, $prototypeOptions);
        }

        return $this->$propertyName = null;
    }

    /**
     * Set lazy loading property
     *
     * @param string $propertyName
     * @param string $interface
     * @param $value
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
     * Test managed configuration
     *
     * @return $this
     */
    protected function testManagedConfiguration(): self
    {
        foreach (static::THIS_SETS as $key => $type) {
            if (isset($this->$key) || $type === Types::NULL || (is_array($type) && in_array(Types::NULL, $type, true))) {
                continue;
            }

            throw new Exception\RuntimeException(sprintf(static::REQUIRED_PROPERTY_EXCEPTION, $key));
        }

        return $this;
    }
}
