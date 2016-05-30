<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Engine;

use Fixin\Resource\Resource;
use Fixin\View\ViewInterface;
use Fixin\View\Helper\HelperInterface;
use Fixin\Base\Exception\InvalidArgumentException;

abstract class Engine extends Resource implements EngineInterface {

    const EXCEPTION_INVALID_HELPER_NAME = "Invalid helper name: '%s'";
    const EXCEPTION_NAME_COLLISION = "Child-variable name collision: '%s'";

    /**
     * @var HelperInterface[]
     */
    protected $helpers = [];

    /**
     * {@inheritDoc}
     * @see \Fixin\View\Engine\EngineInterface::getHelper($name)
     */
    public function getHelper(string $name): HelperInterface {
        return $this->helpers[$name] ?? ($this->helpers[$name] = $this->produceHelper($name));
    }

    /**
     * Make helper instance
     *
     * @param string $name
     * @throws InvalidArgumentException
     * @return HelperInterface
     */
    protected function produceHelper(string $name): HelperInterface {
        if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $name)) {
            return $this->container->clonePrototype('View\Helper\\' . ucfirst($name), [
                HelperInterface::OPTION_ENGINE => $this
            ]);
        }

        throw new InvalidArgumentException(sprintf(static::EXCEPTION_INVALID_HELPER_NAME, $name));
    }

    /**
     * Render children
     *
     * @return array
     */
    protected function renderChildren(ViewInterface $view): array {
        $data = [];
        $dataByObject = new \SplObjectStorage();

        foreach ($view->getChildren() as $name => $child) {
            $data[$name] = $dataByObject[$child] ?? ($dataByObject[$child] = $child->render());
        }

        // Test name collision
        $variables = $view->getVariables();

        if ($names = array_intersect_key($data, $variables)) {
            throw new KeyCollisionException(sprintf(static::EXCEPTION_NAME_COLLISION, implode("', '", array_keys($names))));
        }

        return $data;
    }
}