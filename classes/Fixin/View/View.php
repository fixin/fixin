<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View;

use Fixin\Base\FileSystem\FileResolverInterface;
use Fixin\Resource\Prototype;
use Fixin\View\Engine\EngineInterface;

class View extends Prototype implements ViewInterface
{
    protected const
        DEFAULT_ENGINE = 'View\Engine\JsonEngine',
        EXCEPTION_FILE_RESOLVER_NOT_SET = 'File resolver not set',
        EXCEPTION_UNABLE_TO_RESOLVE_TEMPLATE = "Unable to resolve template '%s'",
        THIS_SETS_LAZY = [
            self::OPTION_ENGINE => EngineInterface::class,
            self::OPTION_FILE_RESOLVER => FileResolverInterface::class
        ];

    /**
     * @var ViewInterface[]
     */
    protected $children = [];

    /**
     * @var EngineInterface|false|null
     */
    protected $engine;

    /**
     * @var FileResolverInterface|false|null
     */
    protected $fileResolver;

    /**
     * @var array
     */
    protected $postfixToEngineMap = [
        '.php' => 'View\Engine\PhpEngine',
        '.phtml' => 'View\Engine\PhpEngine'
    ];

    /**
     * @var string
     */
    protected $template = '';

    /**
     * @var array
     */
    protected $variables = [];

    /**
     * @return static
     */
    public function clearChildren(): ViewInterface
    {
        $this->children = [];

        return $this;
    }

    /**
     * @return static
     */
    public function clearVariables(): ViewInterface
    {
        $this->variables = [];

        return $this;
    }

    public function getChild(string $name): ?ViewInterface
    {
        return $this->children[$name] ?? null;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * Get type of the rendered content
     */
    public function getContentType(): string
    {
        return $this->getEngine()->getContentType();
    }

    protected function getEngine(): EngineInterface
    {
        if ($this->engine) {
            return $this->engine;
        }

        if ($engine = $this->loadLazyProperty(static::OPTION_ENGINE)) {
            return $engine;
        }

        return $this->engine = $this->container->get($this->getEngineNameForTemplate());
    }

    /**
     * Get engine name for the template
     */
    protected function getEngineNameForTemplate(): string
    {
        $template = $this->getResolvedTemplate();

        $start =
        $max = mb_strlen($template);

        while ($start) {
            $start = mb_strrpos($template, '.', $start - $max - 1);
            $postfix = mb_substr($template, $start);

            if (isset($this->postfixToEngineMap[$postfix])) {
                return $this->postfixToEngineMap[$postfix];
            }
        };

        return static::DEFAULT_ENGINE;
    }

    protected function getFileResolver(): FileResolverInterface
    {
        return $this->fileResolver ?: $this->loadLazyProperty(static::OPTION_FILE_RESOLVER);
    }

    /**
     * @throws Exception\RuntimeException
     */
    public function getResolvedTemplate(): string
    {
        $template = $this->template;

        // No template or accessible file
        if ($template === '' || is_file($template)) {
            return $template;
        }

        // Resolving
        $resolved = $this->getFileResolver()->resolve($this->template);

        if (isset($resolved)) {
            // Store resolved filename
            $this->template = $resolved;

            return $resolved;
        }

        throw new Exception\RuntimeException(sprintf(static::EXCEPTION_UNABLE_TO_RESOLVE_TEMPLATE, $template));
    }

    public function getTemplate(): String
    {
        return $this->template;
    }

    public function getVariable(string $name)
    {
        return $this->variables[$name] ?? null;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function render()
    {
        return $this->getEngine()->render($this);
    }

    /**
     * @return static
     */
    public function setChild(string $name, ViewInterface $child): ViewInterface
    {
        $this->children[$name] = $child;

        return $this;
    }

    protected function setPostfixToEngineMap(array $postfixToEngineMap): void
    {
        $this->postfixToEngineMap = $postfixToEngineMap;
    }

    /**
     * @return static
     */
    public function setTemplate(string $template): ViewInterface
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return static
     */
    public function setVariable(string $name, $value): ViewInterface
    {
        $this->variables[$name] = $value;

        return $this;
    }

    /**
     * @return static
     */
    public function setVariables(array $variables): ViewInterface
    {
        $this->variables = $variables + $this->variables;

        return $this;
    }
}
