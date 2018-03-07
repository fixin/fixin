<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\View;

use Fixin\Resource\PrototypeInterface;

interface ViewInterface extends PrototypeInterface
{
    public const
        ENGINE = 'engine',
        FILE_RESOLVER = 'fileResolver',
        POSTFIX_TO_ENGINE_MAP = 'postfixToEngineMap',
        TEMPLATE = 'template',
        VARIABLES = 'variables';

    /**
     * Clear children
     *
     * @return $this
     */
    public function clearChildren(): ViewInterface;

    /**
     * Clear variables
     *
     * @return $this
     */
    public function clearVariables(): ViewInterface;

    /**
     * Get child
     *
     * @param string $name
     * @return ViewInterface|null
     */
    public function getChild(string $name): ?ViewInterface;

    /**
     * Get children
     *
     * @return ViewInterface[]
     */
    public function getChildren(): array;

    /**
     * Get the type of the content
     *
     * @return string
     */
    public function getContentType(): string;

    /**
     * Get resolved template filename
     *
     * @return string
     */
    public function getResolvedTemplate(): string;

    /**
     * Get template name
     *
     * @return string
     */
    public function getTemplate(): string;

    /**
     * Get variable value
     *
     * @param string $name
     * @return mixed
     */
    public function getVariable(string $name);

    /**
     * Get variables
     *
     * @return array
     */
    public function getVariables(): array;

    /**
     * Render
     *
     * @return mixed
     */
    public function render();

    /**
     * Set child
     *
     * @param string $name
     * @param ViewInterface $child
     * @return $this
     */
    public function setChild(string $name, ViewInterface $child): ViewInterface;

    /**
     * Set multiple variables
     *
     * @param array $variables
     * @return $this
     */
    public function setMultipleVariables(array $variables): ViewInterface;

    /**
     * Set template
     *
     * @param string $template
     * @return $this
     */
    public function setTemplate(string $template): ViewInterface;

    /**
     * Set variable
     *
     * @param string $name
     * @param $value
     * @return $this
     */
    public function setVariable(string $name, $value): ViewInterface;
}
