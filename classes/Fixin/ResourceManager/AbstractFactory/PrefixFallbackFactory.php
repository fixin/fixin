<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\ResourceManager\AbstractFactory;

class PrefixFallbackFactory extends AbstractFactory {

    const KEY_SEARCH_ORDER = 'searchOrder';

    /**
     * @var array
     */
    protected $map = [];

    /**
     * @var array
     */
    protected $searchOrder;

    /**
     * {@inheritDoc}
     * @see \Fixin\ResourceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(array $options = null, string $name = null) {
        $mapped = $this->map[$name];

        return $mapped ? new $mapped($this->container, $options, $name) : null;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\ResourceManager\AbstractFactory\AbstractFactoryInterface::canProduce($name)
     */
    public function canProduce(string $name): bool {
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

    /**
     * Set search order
     *
     * @param array $searchOrder
     */
    protected function setSearchOrder(array $searchOrder) {
        $this->searchOrder = $searchOrder;
    }
}