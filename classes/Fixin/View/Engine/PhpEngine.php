<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Engine;

use Fixin\View\ViewInterface;

class PhpEngine extends Engine {

    protected function renderChain(ViewInterface $view) {
        $__data = parent::renderChain($view);

        // Template
        $__template = $view->getResolvedTemplate();
        if (is_null($__template)) {
            return $__data;
        }

        // Extract data
        unset($view, $__data['this']);
        extract($__data);
        unset($__data);

        // Include
        try {
            ob_start();
            include $__template;
            $content = ob_get_clean();
        }
        catch (\Throwable $t) {
            ob_end_clean();

            throw $t;
        }

        return $content;
    }
}