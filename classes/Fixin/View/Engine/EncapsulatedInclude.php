<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Engine;

final class EncapsulatedInclude
{
    /**
     * Include file with own scope
     */
    public static function include(AssistantInterface $_, string $__filename, array $__data): void
    {
        // Extract data
        unset($__data['_'], $__data['__filename']);

        extract($__data);
        unset($__data);

        // Include
        include $__filename;
    }
}
