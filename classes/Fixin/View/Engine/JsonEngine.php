<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Engine;

use Fixin\View\ViewInterface;

class JsonEngine extends Engine {

    /**
     * {@inheritDoc}
     * @see \Fixin\View\Engine\EngineInterface::render()
     */
    public function render(ViewInterface $view) {
        return $this->container->get('Base\Json\Json')->encode($this->fetchData($view));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\Engine\Engine::renderInner($view)
     */
    protected function renderInner(ViewInterface $view) {
        return $this->fetchData($view);
    }
}