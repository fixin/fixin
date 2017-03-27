<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\View\Engine;

interface AssistantInterface
{
    /**
     * Cloned instance with engine
     *
     * @return static
     */
    public function withEngine(EngineInterface $engine): AssistantInterface;
}
