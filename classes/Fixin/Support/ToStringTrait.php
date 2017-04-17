<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
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

        return Ground::toDebugText(get_class($this) . ' {' . ($items ? PHP_EOL . VariableInspector::itemsInfo($items) : '') . '}');
    }
}
