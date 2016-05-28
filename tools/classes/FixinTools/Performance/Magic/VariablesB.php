<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace FixinTools\Performance\Magic;

class VariablesB extends VariablesA {

    /**
     * {@inheritDoc}
     * @see \FixinTools\Performance\Magic\VariablesA::__get($name)
     */
    public function &__get(string $name) {
        if (!array_key_exists($name, $this->variables)) {
            return $this->variables[$name];
        }

        $this->$name = &$this->variables[$name];

        return $this->$name;
    }

    /**
     * {@inheritDoc}
     * @see \FixinTools\Performance\Magic\VariablesA::__set($name, $value)
     */
    public function __set(string $name, $value) {
        $this->variables[$name] = $value;

        $this->$name = &$this->variables[$name];
    }

}