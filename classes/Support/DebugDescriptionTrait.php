<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Support;

trait DebugDescriptionTrait
{
    /**
     * Debug description for the instance
     *
     * @return string
     */
    public function debugDescription(): string
    {
        if (method_exists($this, '__debugInfo')) {
            $items = $this->__debugInfo();
        }
        else {
            $items = [];

            foreach ((array) $this as $key => $value) {
                if ($key[0] === "\0") {
                    $tags = explode("\0", $key);
                    $key = $tags[1] === '*' ? implode($tags) : '@' . implode('->', array_slice($tags, 1));
                }

                $items[$key] = $value;
            }
        }

        return '{' . ($items ? VariableInspector::itemsInfo($items, '') : '') . '}';
    }
}
