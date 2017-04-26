<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace FixinTools\Performance\Magic;

class VariablesB extends VariablesA
{
    public function &__get(string $name)
    {
        if (!array_key_exists($name, $this->variables)) {
            return $this->variables[$name];
        }

        $this->$name = &$this->variables[$name];

        return $this->$name;
    }

    public function __set(string $name, $value): void
    {
        $this->variables[$name] = $value;

        $this->$name = &$this->variables[$name];
    }

}
