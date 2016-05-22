<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View;

use Fixin\Support\PrototypeInterface;
use Fixin\View\Engine\EngineInterface;

interface ViewInterface extends PrototypeInterface, \ArrayAccess {

    /**
     * @return string
     */
    public function __toString(): string;

    /**
     * Clear children
     *
     * @return self
     */
    public function clearChildren();

    /**
     * Clear variables
     *
     * @return self
     */
    public function clearVariables();

    /**
     * Get named child
     *
     * @param string $name
     * @return ViewInterface|null
     */
    public function getChild(string $name);

    /**
     * Get children
     *
     * @return ViewInterface[]
     */
    public function getChildren(): array;

    /**
     * Get engine name or instance
     *
     * @return string|EngineInterface|NULL
     */
    public function getEngine();

    /**
     * Get variable value
     *
     * @param string $name
     * @return mixed|null
     */
    public function getVariable(string $name);

    /**
     * Get variables
     *
     * @return array
     */
    public function getVariables();

    /**
     * Render
     *
     * @return mixed
     */
    public function render();

    /**
     * Set named child
     *
     * @param string $name
     * @param ViewInterface $child
     * @return self
     */
    public function setChild(string $name, ViewInterface $child);

    /**
     * Set engine name or instance
     *
     * @param bool|string $engine
     * @return self
     */
    public function setEngine($engine);

    /**
     * Set template name
     *
     * @param string $template
     * @param bool|string $setEngine
     * @return self
     */
    public function setTemplate(string $template);

    /**
     * Set variable value
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setVariable(string $key, $value);

    /**
     * Set variables
     *
     * @param array $variables
     * @return self
     */
    public function setVariables(array $variables);
}