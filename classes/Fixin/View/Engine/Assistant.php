<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Engine;

use Fixin\View\Helper\HelperInterface;

/**
 * @author attilajenei
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Assistant implements AssistantInterface {

    /**
     * @var EngineInterface
     */
    protected $__engine;

    /**
     * @var Assistant
     */
    protected $__prototype;

    /**
     * @param string $name
     * @return HelperInterface
     */
    public function __get(string $name): HelperInterface {
        return $this->$name = $this->__prototype->getHelper($name);
    }

    /**
     * Resolving helper
     *
     * @param string $name
     * @return HelperInterface
     */
    protected function getHelper(string $name): HelperInterface {
        return $this->$name = $this->__engine->getHelper($name);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\Engine\AssistantInterface::withEngine($engine)
     */
    public function withEngine(EngineInterface $engine): AssistantInterface {
        $clone = clone $this;
        $clone->__engine = $engine;
        $clone->__prototype = $clone;

        return $clone;
    }
}