<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Engine;

use Fixin\Resource\ResourceInterface;
use Fixin\View\Helper\HelperInterface;
use Fixin\View\ViewInterface;

interface EngineInterface extends ResourceInterface {

    /**
     * Get helper
     *
     * @param string $name
     * @return HelperInterface
     */
    public function getHelper(string $name): HelperInterface;

    /**
     * Render view
     *
     * @param ViewInterface $view
     * @return mixed
     */
    public function render(ViewInterface $view);
}