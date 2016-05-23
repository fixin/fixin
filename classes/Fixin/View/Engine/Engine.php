<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Engine;

use Fixin\ResourceManager\Resource;
use Fixin\View\ViewInterface;

class Engine extends Resource implements EngineInterface {

    const EXCEPTION_NAME_COLLISION = "Child-variable name collision: '%s'";

    /**
     * {@inheritDoc}
     * @see \Fixin\View\Engine\EngineInterface::render()
     */
    public function render(ViewInterface $view) {
        return $this->renderChain($view);
    }

    /**
     * Render chain method
     *
     * @param ViewInterface $view
     * @throws KeyCollisionException
     * @return mixed
     */
    protected function renderChain(ViewInterface $view) {
        // Children
        $data = [];
        $dataByObject = new \SplObjectStorage();

        foreach ($view->getChildren() as $name => $child) {
            $data[$name] = $dataByObject[$child] ?? ($dataByObject[$child] = $this->renderChain($child));
        }

        // Variables
        $variables = $view->getVariables();

        if ($names = array_intersect_key($data, $variables)) {
            throw new KeyCollisionException(sprintf(static::EXCEPTION_NAME_COLLISION, implode("', '", array_keys($names))));
        }

        return $this->renderChainProcess($view, $data + $variables);
    }

    protected function renderChainProcess(ViewInterface $view, array $data) {
        return $data;
    }
}