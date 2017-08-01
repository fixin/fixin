<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Resource\AbstractFactory;

use Fixin\Resource\Managed;
use Fixin\Support\Types;

class NamespaceFallbackFactory extends Managed implements AbstractFactoryInterface
{
    protected const
        THIS_SETS = [
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

    public function __invoke(array $options = null, string $name = null)
    {
        $mapped = $this->map[$name];

        return $mapped ? new $mapped($this->resourceManager, $options, $name) : null;
    }

    public function canProduce(string $name): bool
    {
        if ($name[0] !== '*' || $name[1] !== '\\') {
            return false;
        }

        // Already resolved
        if (isset($this->map[$name])) {
            return (bool) $this->map[$name];
        }

        // Mapping
        $nameTail = '\\' . substr($name, 2);

        foreach ($this->searchOrder as $rootNamespace) {
            if (class_exists($className = $rootNamespace . $nameTail)) {
                $this->map[$name] = $className;

                return true;
            }
        }

        // Not found
        return $this->map[$name] = false;
    }
}
