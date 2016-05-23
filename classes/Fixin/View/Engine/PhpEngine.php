<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Engine;

use Fixin\View\ViewInterface;

class PhpEngine extends Engine {

    /**
     * {@inheritDoc}
     * @see \Fixin\View\Engine\EngineInterface::render()
     */
    public function render(ViewInterface $view) {
        return $this->renderInner($view);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\Engine\Engine::renderInner($view)
     */
    protected function renderInner(ViewInterface $view) {
        $data = $this->fetchData($view);

        // Template
        $filename = $view->getResolvedTemplate();
        if (is_null($filename)) {
            return $data;
        }

        // Include
        try {
            ob_start();
            EncapsulatedInclude::include($this, $filename, $data);
        }
        catch (\Throwable $t) {
            ob_end_clean();

            throw $t;
        }

        return ob_get_clean();
    }
}