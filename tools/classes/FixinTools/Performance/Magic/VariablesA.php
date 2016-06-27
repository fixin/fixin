<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace FixinTools\Performance\Magic;

/**
 * @property callable summary
 * @property callable summary1
 * @property callable summary2
 * @property callable summary3
 */
class VariablesA implements \ArrayAccess {

    protected $test = 'test';

    /**
     * @var array
     */
    protected $variables = [
        'summary' => 'This is a test summary.',
        'x' => ['a' => 0]
    ];

    /**
     * @param string $name
     * @return mixed
     */
    public function &__get(string $name) {
        return $this->variables[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool {
        return isset($this->variables[$name]);
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value) {
        $this->variables[$name] = $value;
    }

    /**
     * Simple getter
     *
     * @return string
     */
    public function getTest() {
        return $this->test;
    }

    /**
     * Get variable value
     *
     * @param string $name
     * @return mixed|null
     */
    public function getVariable(string $name) {
        return $this->variables[$name] ?? null;
    }

    /**
     * Get variables
     *
     * @return array
     */
    public function getVariables(): array {
        return $this->variables;
    }

    /**
     * Has variable
     *
     * @param string $name
     * @return bool
     */
    public function hasVariable(string $name): bool {
        return isset($this->variables[$name]);
    }

    /**
     * {@inheritDoc}
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset) {
        return isset($this->variables[$offset]);
    }

    /**
     * {@inheritDoc}
     * @see ArrayAccess::offsetGet()
     */
    public function &offsetGet($offset) {
        return $this->variables[$offset];
    }

    /**
     * {@inheritDoc}
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value) {
        $this->variables[$offset] = $value;
    }

    /**
     * {@inheritDoc}
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset) {
        unset($this->variables[$offset]);
    }

    /**
     * Set variable value
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setVariable(string $key, $value) {
        $this->variables[$key] = $value;

        return $this;
    }

    /**
     * Set variables
     *
     * @param array $variables
     * @return self
     */
    public function setVariables(array $variables) {
        $this->variables = $variables + $this->variables;

        return $this;
    }
}