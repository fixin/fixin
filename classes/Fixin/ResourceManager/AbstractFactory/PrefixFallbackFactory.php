<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\ResourceManager\AbstractFactory;

use Fixin\Support\ContainerInterface;

class PrefixFallbackFactory implements AbstractFactoryInterface {

    const RESOURCE_NAME_KEY = 'resourceName';
    const SEARCH_ORDER_KEY = 'searchOrder';

    /**
     * @var array
     */
    protected $map = [];

    /**
     * @var array
     */
    protected $searchOrder;

    /**
     * @param ContainerInterface $container
     * @param array $options
     */
    public function __construct(ContainerInterface $container, array $options = []) {
        // Search order
        $this->searchOrder = $options[static::SEARCH_ORDER_KEY] ?? ['Fixin'];
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\ResourceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, string $name) {
        $mapped = $this->map[$name];

        return $mapped ? new $mapped($container, [static::RESOURCE_NAME_KEY => $name]) : null;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\ResourceManager\AbstractFactory\AbstractFactoryInterface::canProduce($manager, $name)
     */
    public function canProduce(ContainerInterface $container, string $name): bool {
        // Already resolved
        if (isset($this->map[$name])) {
            return (bool) $this->map[$name];
        }

        // Mapping
        foreach ($this->searchOrder as $prefix) {
            $className = $prefix . '\\' . $name;

            if (class_exists($className)) {
                $this->map[$name] = $className;

                return true;
            }
        }

        // Not found
        return $this->map[$name] = false;
    }
}