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
    public function __toString();

    /**
     * Get named child
     *
     * @param string $name
     * @return ViewInterface|null
     */
    public function getChild(string $name);

    /**
     * Get data
     *
     * @return array
     */
    public function getData();

    /**
     * Get engine name or instance
     *
     * @return string|EngineInterface|NULL
     */
    public function getEngine();

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
}