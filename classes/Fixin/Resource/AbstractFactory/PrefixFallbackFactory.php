<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Resource\AbstractFactory;

use Fixin\Resource\Resource;

class PrefixFallbackFactory extends AbstractFactory {

    const EXCEPTION_SEARCH_ORDER_NOT_SET = 'Search order not set';
    const OPTION_SEARCH_ORDER = 'searchOrder';

    /**
     * @var array
     */
    protected $map = [];

    /**
     * @var string[]
     */
    protected $searchOrder = [];

    /**
     * {@inheritDoc}
     * @see \Fixin\Resource\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(array $options = null, string $name = null) {
        $mapped = $this->map[$name];

        return $mapped ? new $mapped($this->container, $options, $name) : null;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Resource\AbstractFactory\AbstractFactoryInterface::canProduce($name)
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
     * {@inheritDoc}
     * @see \Fixin\Resource\Resource::configurationTests()
     */
    protected function configurationTests(): Resource {
        if (empty($this->searchOrder)) {
            throw new RuntimeException(static::EXCEPTION_SEARCH_ORDER_NOT_SET);
        }

        return $this;
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