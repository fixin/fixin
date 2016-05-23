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
     * @param EngineInterface $this
     * @param string $filename
     * @param array $data
     * @return mixed
     */
    public static function include(EngineInterface $this, string $filename, array $data) {
        // Extract data
        $__data = $data;
        $__filename = $filename;
        unset($data, $__data['this'], $filename);

        extract($__data);
        unset($__data);

        // Include
        return include $__filename;
    }
}