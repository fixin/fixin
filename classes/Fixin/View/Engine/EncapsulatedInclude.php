<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Engine;

final class EncapsulatedInclude {

    /**
     * Include file with own scope
     *
     * @param AssistantInterface $_
     * @param string $__filename
     * @param array $__data
     * @return mixed
     */
    public static function include(AssistantInterface $_, string $__filename, array $__data) {
        // Extract data
        unset($__data['_']);

        extract($__data);
        unset($__data);

        // Include
        include $__filename;
    }
}