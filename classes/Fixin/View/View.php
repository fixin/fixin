<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\View;

use Fixin\Base\FileSystem\FileResolverInterface;
use Fixin\Resource\Prototype;
use Fixin\Support\Types;
use Fixin\View\Engine\EngineInterface;

class View extends Prototype implements ViewInterface
{
    protected const
        DEFAULT_ENGINE = 'View\Engine\JsonEngine',
        FILE_RESOLVER_NOT_SET_EXCEPTION = 'File resolver not set',
        UNABLE_TO_RESOLVE_TEMPLATE_EXCEPTION = "Unable to resolve template '%s'",
        THIS_SETS = [
            self::ENGINE => [self::LAZY_LOADING => EngineInterface::class, Types::NULL],
            self::FILE_RESOLVER => [self::LAZY_LOADING => FileResolverInterface::class, Types::NULL],
            self::POSTFIX_TO_ENGINE_MAP => Types::ARRAY,
            self::TEMPLATE => self::USING_SETTER
        ];

    /**
     * @var ViewInterface[]
     */
    protected $children = [];

    /**
     * @var EngineInterface|false
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
     * @return $this
     */
    public function clearChildren(): ViewInterface
    {
        $this->children = [];

        return $this;
    }

    /**
     * @return $this
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

    public function getContentType(): string
    {
        return $this->getEngine()->getContentType();
    }

    protected function getEngine(): EngineInterface
    {
        if ($this->engine) {
            return $this->engine;
        }

        if ($engine = $this->loadLazyProperty(static::ENGINE)) {
            return $engine;
        }

        return $this->engine = $this->resourceManager->get($this->getEngineNameForTemplate(), EngineInterface::class);
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
        return $this->fileResolver ?: $this->loadLazyProperty(static::FILE_RESOLVER);
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

        throw new Exception\RuntimeException(sprintf(static::UNABLE_TO_RESOLVE_TEMPLATE_EXCEPTION, $template));
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
     * @return $this
     */
    public function replaceVariables(array $variables): ViewInterface
    {
        $this->variables = $variables + $this->variables;

        return $this;
    }

    /**
     * @return $this
     */
    public function setChild(string $name, ViewInterface $child): ViewInterface
    {
        $this->children[$name] = $child;

        return $this;
    }

    /**
     * @return $this
     */
    public function setTemplate(string $template): ViewInterface
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return $this
     */
    public function setVariable(string $name, $value): ViewInterface
    {
        $this->variables[$name] = $value;

        return $this;
    }
}
