<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\View\Engine;

use Fixin\View\Helper\HelperInterface;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Assistant implements AssistantInterface
{
    /**
     * @var EngineInterface
     */
    protected $__engine;

    /**
     * @var Assistant
     */
    protected $__prototype;

    public function __get(string $name): HelperInterface
    {
        return $this->$name = $this->__prototype->getHelper($name);
    }

    /**
     * Resolving helper
     */
    protected function getHelper(string $name): HelperInterface
    {
        return $this->$name = $this->__engine->getHelper($name);
    }

    /**
     * @return static
     */
    public function withEngine(EngineInterface $engine): AssistantInterface
    {
        $clone = clone $this;
        $clone->__engine = $engine;
        $clone->__prototype = $clone;

        return $clone;
    }
}
