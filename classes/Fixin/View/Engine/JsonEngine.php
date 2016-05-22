<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Engine;

use Fixin\Base\Exception\KeyCollisionException;
use Fixin\ResourceManager\Resource;
use Fixin\View\ViewInterface;

class JsonEngine extends Resource implements EngineInterface {

    const EXCEPTION_NAME_COLLISION = "Child-variable name collision: '%s'";

    /**
     * {@inheritDoc}
     * @see \Fixin\View\Engine\EngineInterface::render()
     */
    public function render(ViewInterface $view) {
        return $this->container->get('Base\Json\Json')->encode($this->renderArray($view));
    }

    /**
     * @param ViewInterface $view
     * @throws KeyCollisionException
     * @return array
     */
    protected function renderArray(ViewInterface $view): array {
        // Children
        $data = [];
        $dataByObject = new \SplObjectStorage();

        foreach ($view->getChildren() as $name => $child) {
            if (isset($dataByObject[$child])) {
                $data[$name] = $dataByObject[$child];

                continue;
            }

            $data[$name] =
            $dataByObject[$child] = $this->renderArray($child);
        }

        // Variables
        $variables = $view->getVariables();

        if ($names = array_intersect_key($data, $variables)) {
            throw new KeyCollisionException(sprintf(static::EXCEPTION_NAME_COLLISION, implode("', '", array_keys($names))));
        }

        return $data + $variables;
    }
}