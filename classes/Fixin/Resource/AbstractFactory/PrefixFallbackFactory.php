<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Resource\AbstractFactory;

use Fixin\Resource\Resource;

class PrefixFallbackFactory extends Resource implements AbstractFactoryInterface {

    const OPTION_SEARCH_ORDER = 'searchOrder';
    const THIS_REQUIRES = [
        self::OPTION_SEARCH_ORDER => self::TYPE_ARRAY
    ];

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
     * @see \Fixin\Resource\AbstractFactory\AbstractFactoryInterface::__invoke($options, $name)
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
     * Set search order
     *
     * @param array $searchOrder
     */
    protected function setSearchOrder(array $searchOrder) {
        $this->searchOrder = $searchOrder;
    }
}