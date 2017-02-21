<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View;

use Fixin\Resource\PrototypeInterface;

interface ViewInterface extends PrototypeInterface
{
    public const
        OPTION_ENGINE = 'engine',
        OPTION_FILE_RESOLVER = 'fileResolver',
        OPTION_POSTFIX_TO_ENGINE_MAP = 'postfixToEngineMap';

    public function clearChildren(): ViewInterface;
    public function clearVariables(): ViewInterface;
    public function getChild(string $name): ?ViewInterface;

    /**
     * @return ViewInterface[]
     */
    public function getChildren(): array;

    /**
     * Get resolved template filename
     */
    public function getResolvedTemplate(): string;

    public function getTemplate(): string;
    public function getVariable(string $name);
    public function getVariables(): array;
    public function render();
    public function setChild(string $name, ViewInterface $child): ViewInterface;
    public function setTemplate(string $template): ViewInterface;
    public function setVariable(string $name, $value): ViewInterface;
    public function setVariables(array $variables): ViewInterface;
}
