<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Resource\AbstractFactory;

use Fixin\Support\Types;

class NamespaceFallbackFactory extends AbstractFactory
{
    protected const
        THIS_SETS = parent::THIS_SETS + [
            self::SEARCH_ORDER => Types::ARRAY
        ];

    public const
        SEARCH_ORDER = 'searchOrder';

    /**
     * @var bool[]|string[]
     */
    protected $map = [];

    /**
     * @var string[]
     */
    protected $searchOrder = [];

    protected function canProduce(string $key): bool
    {
        if ($key[0] !== '*' || $key[1] !== '\\') {
            return false;
        }

        // Already resolved
        if (isset($this->map[$key])) {
            return (bool) $this->map[$key];
        }

        // Mapping
        $nameTail = '\\' . substr($key, 2);

        foreach ($this->searchOrder as $rootNamespace) {
            if (class_exists($className = $rootNamespace . $nameTail)) {
                $this->map[$key] = $className;

                return true;
            }
        }

        // Not found
        return $this->map[$key] = false;
    }

    protected function produce(string $key, array $options, string $name)
    {
        $mapped = $this->map[$key];

        return $mapped ? new $mapped($this->resourceManager, $options, $name) : null;
    }
}
