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

    public function render(ViewInterface $view) {
        return $this->renderChained($view);
    }

    protected function renderChained(ViewInterface $view) {
        return $this->renderView($view);
    }

    protected function renderView(ViewInterface $view) {
        // Children
        $data = [];
        $dataByObject = new \SplObjectStorage();

        foreach ($view->getChildren() as $name => $child) {
            $data[$name] = $dataByObject[$child] ?? ($dataByObject[$child] = $this->renderChained($child));
        }

        // Variables
        $variables = $view->getVariables();

        if ($names = array_intersect_key($data, $variables)) {
            throw new KeyCollisionException(sprintf(static::EXCEPTION_NAME_COLLISION, implode("', '", array_keys($names))));
        }

        return $data + $variables;
    }
}