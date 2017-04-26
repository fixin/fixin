<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace FixinTools\Performance\Magic;

/**
 * @property callable escapeHtml
 */
class MethodsB extends MethodsA
{
    public function __get(string $name)
    {
        return $this->$name = $this->helpers[$name] ?? null;
    }
}
