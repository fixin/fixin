<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View;

use Fixin\Resource\PrototypeInterface;

interface ViewInterface extends PrototypeInterface {

    const OPTION_ENGINE = 'engine';
    const OPTION_POSTFIX_ENGINE_MAP = 'postfixEngineMap';
    const OPTION_FILE_RESOLVER = 'fileResolver';

    /**
     * Clear children
     *
     * @return self
     */
    public function clearChildren(): ViewInterface;

    /**
     * Clear variables
     *
     * @return self
     */
    public function clearVariables(): ViewInterface;

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
     * Get resolved template filename
     *
     * @return string
     */
    public function getResolvedTemplate(): string;

    /**
     * Get template
     *
     * @return string
     */
    public function getTemplate(): string;

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
    public function getVariables(): array;

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
    public function setChild(string $name, ViewInterface $child): ViewInterface;

    /**
     * Set template name
     *
     * @param string $template
     * @return self
     */
    public function setTemplate(string $template): ViewInterface;

    /**
     * Set variable value
     *
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function setVariable(string $name, $value): ViewInterface;

    /**
     * Set variables
     *
     * @param array $variables
     * @return self
     */
    public function setVariables(array $variables): ViewInterface;
}