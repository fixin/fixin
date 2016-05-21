<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View;

use Fixin\ResourceManager\Resource;
use Fixin\Base\Exception\InvalidParameterException;
use Fixin\View\Engine\EngineInterface;

class View extends Resource implements ViewInterface {

    const DEFAULT_ENGINE = 'View\Engine\Json';
    const INVALID_ENGINE_PARAMETER = 'Invalid engine parameter';

    /**
     * @var ViewInterface[]
     */
    protected $children = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var string|EngineInterface|null
     */
    protected $engine;

    /**
     * @var array
     */
    protected $extensions = [
        '.php' => 'php',
        '.phtml' => 'php'
    ];

    /**
     * @var string|null
     */
    protected $template;

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::__toString()
     */
    public function __toString() {
        return $this->render();
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::getChild()
     */
    public function getChild(string $name) {
        return $this->children[$name] ?? null;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::getData()
     */
    public function getData() {
        return $this->data;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::getEngine()
     */
    public function getEngine() {
        return $this->engine;
    }

    /**
     * {@inheritDoc}
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset) {
        return isset($this->data[$offset]);
    }

    /**
     * {@inheritDoc}
     * @see ArrayAccess::offsetGet()
     */
    public function &offsetGet($offset) {
        $ret =& $this->storage[$offset];

        return $ret;
    }

    /**
     * {@inheritDoc}
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value) {
        if ($offset === null) {
            $this->data[] = $value;

            return;
        }

        $this->data[$offset] = $value;
    }

    /**
     * {@inheritDoc}
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset) {
        unset($this->data[$offset]);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::render()
     */
    public function render() {

        /*$engine = $this->engie;

        if (is_string($engine)) {
            $engine = $this->container->get($engine);
        }

        if (is_string($this->engine)) {

        }

        return $this->en
/*

        $renders = [];
        $rendersByObject = new \SplObjectStorage();

        foreach ($this->children as $name => $child) {
            if (isset($rendersByObject[$child])) {
                $renders[$name] = $rendersByObject[$child];

                continue;
            }

            $renders[$name] =
            $rendersByObject[$child] = $child->render();
        }

        return $renders + $this->data;*/
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::setChild()
     */
    public function setChild(string $name, ViewInterface $child) {
        $this->children[$name] = $child;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::setEngine()
     */
    public function setEngine($engine) {
        if (isset($engine) && !is_string($engine) && !$engine instanceof EngineInterface) {
            throw new InvalidParameterException(static::INVALID_ENGINE_PARAMETER);
        }

        $this->engine = $setEngine;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::setTemplate()
     */
    public function setTemplate(string $template) {
        $this->template = $template;

        return $this;
    }
}
