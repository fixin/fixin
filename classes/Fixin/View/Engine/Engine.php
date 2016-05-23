<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Engine;

use Fixin\ResourceManager\Resource;
use Fixin\View\ViewInterface;

abstract class Engine extends Resource implements EngineInterface {

    const EXCEPTION_NAME_COLLISION = "Child-variable name collision: '%s'";

    /**
     * Fetch data for view
     *
     * @param ViewInterface $view
     * @throws KeyCollisionException
     * @return array
     */
    protected function fetchData(ViewInterface $view): array {
        // Children
        $data = [];
        $dataByObject = new \SplObjectStorage();

        foreach ($view->getChildren() as $name => $child) {
            $data[$name] = $dataByObject[$child] ?? ($dataByObject[$child] = $this->renderInner($child));
        }

        // Variables
        $variables = $view->getVariables();

        if ($names = array_intersect_key($data, $variables)) {
            throw new KeyCollisionException(sprintf(static::EXCEPTION_NAME_COLLISION, implode("', '", array_keys($names))));
        }

        return $data + $variables;
    }

    /**
     * Render chain
     *
     * @param ViewInterface $view
     * @return mixed
     */
    abstract protected function renderInner(ViewInterface $view);
}