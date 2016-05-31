<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View;

use Fixin\Base\Exception\RuntimeException;
use Fixin\Base\FileSystem\FileResolverInterface;
use Fixin\Resource\Prototype;
use Fixin\View\Engine\EngineInterface;

class View extends Prototype implements ViewInterface {

    const DEFAULT_ENGINE = 'View\Engine\JsonEngine';

    const EXCEPTION_FILE_RESOLVER_NOT_SET = 'File resolver not set';
    const EXCEPTION_UNABLE_TO_RESOLVE_TEMPLATE = "Unable to resolve template '%s'";

    /**
     * @var ViewInterface[]
     */
    protected $children = [];

    /**
     * @var EngineInterface|false|null
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
     * @var FileResolverInterface|false|null
     */
    protected $fileResolver;

    /**
     * @var string
     */
    protected $template = '';

    /**
     * @var array
     */
    protected $variables = [];

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::clearChildren()
     */
    public function clearChildren(): ViewInterface {
        $this->children = [];

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::clearVariables()
     */
    public function clearVariables(): ViewInterface {
        $this->variables = [];

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::getChild($name)
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
     * Get Engine instance
     *
     * @return EngineInterface
     */
    protected function getEngine(): EngineInterface {
        if ($this->engine) {
            return $this->engine;
        }

        if ($engine = $this->loadLazyProperty('engine')) {
            return $engine;
        }

        return $this->engine = $this->container->get($this->getEngineNameForTemplate());
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
     * Get FileResolver instance
     *
     * @return FileResolverInterface
     */
    protected function getFileResolver(): FileResolverInterface {
        return $this->fileResolver ?: $this->loadLazyProperty('fileResolver');
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::getResolvedTemplate()
     */
    public function getResolvedTemplate(): string {
        $template = $this->template;

        // No template or accessible file
        if (mb_strlen($template) === 0 || is_file($template)) {
            return $template;
        }

        // Resolving
        $resolved = $this->getFileResolver()->resolve($this->template);

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
    public function getTemplate(): String {
        return $this->template;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::getVariable($name)
     */
    public function getVariable(string $name) {
        return $this->variables[$name] ?? null;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::getVariables()
     */
    public function getVariables(): array {
        return $this->variables;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::render()
     */
    public function render() {
        return $this->getEngine()->render($this);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::setChild($name, $child)
     */
    public function setChild(string $name, ViewInterface $child): ViewInterface {
        $this->children[$name] = $child;

        return $this;
    }

    /**
     * Set Engine
     *
     * @param string|EngineInterface $engine
     */
    protected function setEngine($engine) {
        $this->setLazyLoadingProperty('engine', EngineInterface::class, $engine);
    }

    /**
     * Set FileResolver
     *
     * @param string|FileResolverInterface $fileResolver
     */
    protected function setFileResolver($fileResolver) {
        $this->setLazyLoadingProperty('fileResolver', FileResolverInterface::class, $fileResolver);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::setTemplate($template)
     */
    public function setTemplate(string $template): ViewInterface {
        $this->template = $template;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::setVariable($name, $value)
     */
    public function setVariable(string $name, $value): ViewInterface {
        $this->variables[$name] = $value;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\ViewInterface::setVariables($variables)
     */
    public function setVariables(array $variables): ViewInterface {
        $this->variables = $variables + $this->variables;

        return $this;
    }
}