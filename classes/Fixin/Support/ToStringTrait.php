<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Support;

trait ToStringTrait
{
    /**
     * Return readable list of variables of instance
     */
    public function __toString(): string
    {
        $items = method_exists($this, '__debugInfo') ? $this->__debugInfo() : (array) $this;

        return Ground::debugText(get_class($this) . ' {' . ($items ? "\n" . VariableInspector::itemsInfo($items, '#444') : '') . '}');
    }
}
