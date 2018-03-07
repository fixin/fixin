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

    /**
     * @inheritDoc
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array($this->$name, $arguments);
    }

    /**
     * @inheritDoc
     */
    public function __get(string $name): HelperInterface
    {
        return $this->$name = $this->__prototype->getHelper($name);
    }

    /**
     * Get helper
     *
     * @param string $name
     * @return HelperInterface
     */
    protected function getHelper(string $name): HelperInterface
    {
        return $this->$name = $this->__engine->getHelper($name);
    }

    /**
     * @inheritDoc
     */
    public function withEngine(EngineInterface $engine): AssistantInterface
    {
        $clone = clone $this;
        $clone->__engine = $engine;
        $clone->__prototype = $clone;

        return $clone;
    }
}
