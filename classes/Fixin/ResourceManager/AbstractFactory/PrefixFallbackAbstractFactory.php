<?php

namespace Fixin\ResourceManager\AbstractFactory;

use Fixin\Support\ContainerInterface;

class PrefixFallbackAbstractFactory implements AbstractFactoryInterface {

    /**
     * Resolved names to class names
     *
     * @var array
     */
    protected $map = [];

    /**
     * Search order for fallback
     *
     * @var array
     */
    protected $searchOrder = ['Fixin'];

    /**
     * @param ContainerInterface $container
     * @param array $options
     */
    public function __construct(ContainerInterface $container, array $options = []) {
        // Search order
        $this->searchOrder = $options['searchOrder'] ?? null;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\ResourceManager\AbstractFactory\AbstractFactoryInterface::canProduce($manager, $name)
     */
    public function canProduce(ContainerInterface $container, string $name): bool {
        // Alread resolved
        if (($result = $this->map[$name] ?? null) !== null) {
            return $result;
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
        $this->map[$name] = false;

        return false;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\ResourceManager\AbstractFactory\AbstractFactoryInterface::produce($manager, $name)
     */
    public function produce(ContainerInterface $container, string $name) {
        $mapped = $this->map[$name];

        return $mapped ? new $mapped($container, ['resourceName' => $name]) : null;
    }
}