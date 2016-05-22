<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Engine;

use Fixin\View\ViewInterface;

interface EngineInterface {

    /**
     * Render view
     *
     * @param ViewInterface $view
     * @return mixed
     */
    public function render(ViewInterface $view);
}