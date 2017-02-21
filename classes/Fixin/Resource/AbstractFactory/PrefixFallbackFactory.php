<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Resource\AbstractFactory;

use Fixin\Resource\Resource;

class PrefixFallbackFactory extends Resource implements AbstractFactoryInterface
{
    protected const
        THIS_REQUIRES = [
            self::OPTION_SEARCH_ORDER => self::TYPE_ARRAY
        ];

    public const
        OPTION_SEARCH_ORDER = 'searchOrder';

    /**
     * @var array
     */
    protected $map = [];

    /**
     * @var string[]
     */
    protected $searchOrder = [];

    public function __invoke(array $options = null, string $name = null)
    {
        $mapped = $this->map[$name];

        return $mapped ? new $mapped($this->container, $options, $name) : null;
    }

    public function canProduce(string $name): bool
    {
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

    protected function setSearchOrder(array $searchOrder): void
    {
        $this->searchOrder = $searchOrder;
    }
}
