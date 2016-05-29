<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Engine;

interface AssistantInterface {

    /**
     * New instance for engine
     *
     * @param EngineInterface $engine
     * @return self
     */
    public function withEngine(EngineInterface $engine): AssistantInterface;
}