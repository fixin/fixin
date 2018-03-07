<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\View\Engine;

final class EncapsulatedInclude
{
    /**
     * Include file with its own scope
     *
     * @param AssistantInterface $_
     * @param string $__filename
     * @param array $__data
     */
    public static function include(AssistantInterface $_, string $__filename, array $__data): void
    {
        // Extract data
        extract($__data, EXTR_SKIP);
        unset($__data);

        // Include
        include $__filename;
    }
}
