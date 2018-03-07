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
    public const
        SEARCH_ORDER = 'searchOrder';

    protected const
        THIS_SETS = parent::THIS_SETS + [
            self::SEARCH_ORDER => Types::ARRAY
        ];

    /**
     * @var bool[]|string[]
     */
    protected $map = [];

    /**
     * @var string[]
     */
    protected $searchOrder = [];

    /**
     * @inheritDoc
     */
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

    /**
     * @inheritDoc
     */
    protected function produce(string $key, array $options)
    {
        $mapped = $this->map[$key];

        // TODO chain to the next?
        return $mapped ? new $mapped($this->resourceManager, $options) : null;
    }
}
