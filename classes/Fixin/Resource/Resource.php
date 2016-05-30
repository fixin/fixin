<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Resource;

use Fixin\Base\Exception\InvalidArgumentException;

abstract class Resource implements ResourceInterface {

    const EXCEPTION_INVALID_OPTION = "Invalid option name '%s' for '%s'";

    /**
     * @var ResourceManagerInterface
     */
    protected $container;

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
        $this->configureWithOptions($options ?? []);
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

            throw new InvalidArgumentException(sprintf(static::EXCEPTION_INVALID_OPTION, $key, get_class($this)));
        }

        return $this;
    }
}