<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Engine;

use Fixin\View\ViewInterface;

class JsonEngine extends Engine {

    const EXCEPTION_NAME_COLLISION = "Child-variable name collision: '%s'";

    /**
     * {@inheritDoc}
     * @see \Fixin\View\Engine\EngineInterface::render()
     */
    public function render(ViewInterface $view) {
        return $this->container->get('Base\Json\Json')->encode($this->renderChain($view));
    }
}