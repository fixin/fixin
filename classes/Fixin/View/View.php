<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View;

use Fixin\Base\Exception\RuntimeException;
use Fixin\Base\FileResolver\FileResolverInterface;
use Fixin\ResourceManager\Resource;
use Fixin\View\Engine\EngineInterface;

class View extends Resource implements ViewInterface {

    const DEFAULT_ENGINE = 'View\Engine\JsonEngine';
    const EXCEPTION_INVALID_ENGINE_ARGUMENT = "Invalid engine argument: string or EngineInterface allowed";
    const EXCEPTION_UNABLE_TO_RESOLVE_TEMPLATE = "Unable to resolve template '%s'";

    /**
     * @var ViewInterface[]
     */
    protected $children = [];

    /**
     * @var string|EngineInterface|null
     */
    protected $engine;

    /**
     * @var array
     */
    protected $engineByPostfix = [
        '.php' => 'View\Engine\PhpEngine',
        '.phtml' => 'View\Engine\PhpEngine'
    ];

    /**
     * @var FileResolverInterface
     */
    protected $fileResolver;

    /**
     * @var string|null
     */
    protected $template;

    /**
     * @var array
     */
    protected $variables = [];

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::clearChildren()
     */
    public function clearChildren() {
        $this->children = [];

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::clearVariables()
     */
    public function clearVariables() {
        $this->variables = [];

        return $this;
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
     * @see \Fixin\View\ViewInterface::getChildren()
     */
    public function getChildren(): array {
        return $this->children;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::getEngine()
     */
    public function getEngine() {
        return $this->engine;
    }

    /**
     * Get engine name for the template
     *
     * @return string
     */
    protected function getEngineNameForTemplate(): string {
        $template = $this->getResolvedTemplate();

        $start =
        $max = mb_strlen($template);

        while ($start) {
            $start = mb_strrpos($template, '.', $start - $max - 1);
            $postfix = mb_substr($template, $start);

            if (isset($this->engineByPostfix[$postfix])) {
                return $this->engineByPostfix[$postfix];
            }
        };

        return static::DEFAULT_ENGINE;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::getResolvedEngine()
     */
    public function getResolvedEngine(): EngineInterface {
        $engine = $this->engine;

        // Resolved
        if ($engine instanceof EngineInterface) {
            return $engine;
        }

        return $this->engine = $this->container->get($engine ?? $this->getEngineNameForTemplate());
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::getResolvedTemplate()
     */
    public function getResolvedTemplate() {
        $template = $this->template;

        // No template
        if (mb_strlen($template) === 0) {
            return null;
        }

        // Accessible file
        if (is_file($template)) {
            return $template;
        }

        // Resolving
        $resolved = ($this->fileResolver ?? ($this->fileResolver = $this->container->get('View\View\FileResolver')))->resolve($this->template);

        if (isset($resolved)) {
            // Store resolved filename
            $this->template = $resolved;

            return $resolved;
        }

        throw new RuntimeException(sprintf(static::EXCEPTION_UNABLE_TO_RESOLVE_TEMPLATE, $template));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::getTemplate()
     */
    public function getTemplate() {
        return $this->template;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::getVariable()
     */
    public function getVariable(string $name) {
        return $this->variables[$name] ?? null;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::getData()
     */
    public function getVariables(): array {
        return $this->variables;
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
        $ret = & $this->variables[$offset];

        return $ret;
    }

    /**
     * {@inheritDoc}
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->variables[] = $value;

            return;
        }

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
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::render()
     */
    public function render() {
        return $this->getResolvedEngine()->render($this);
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
            throw new InvalidArgumentException(static::EXCEPTION_INVALID_ENGINE_ARGUMENT);
        }

        $this->engine = $engine;

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

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::setVariable()
     */
    public function setVariable(string $key, $value) {
        $this->variables[$key] = $value;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::setVariables()
     */
    public function setVariables(array $variables) {
        $this->variables = $variables + $this->variables;

        return $this;
    }
}