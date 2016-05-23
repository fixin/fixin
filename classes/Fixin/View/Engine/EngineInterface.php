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

/**
 * Include file with own scope
 *
 * @param AssistantInterface $_
 * @param string $__filename
 * @param array $__data
 */
function fixinViewEngineEncapsulatedInclude(AssistantInterface $_, string $__filename, array $__data) {
    // Extract data
    unset($__data['_']);

    extract($__data);
    unset($__data);

    // Include
    include $__filename;
}