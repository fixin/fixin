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
     * @return $this
     */
    public function clearChildren(): ViewInterface;

    /**
     * @return $this
     */
    public function clearVariables(): ViewInterface;

    public function getChild(string $name): ?ViewInterface;

    /**
     * @return ViewInterface[]
     */
    public function getChildren(): array;

    /**
     * Get the type of the content
     */
    public function getContentType(): string;

    /**
     * Get resolved template filename
     */
    public function getResolvedTemplate(): string;

    public function getTemplate(): string;
    public function getVariable(string $name);
    public function getVariables(): array;
    public function render();

    /**
     * @return $this
     */
    public function replaceVariables(array $variables): ViewInterface;

    /**
     * @return $this
     */
    public function setChild(string $name, ViewInterface $child): ViewInterface;

    /**
     * @return $this
     */
    public function setTemplate(string $template): ViewInterface;

    /**
     * @return $this
     */
    public function setVariable(string $name, $value): ViewInterface;
}
