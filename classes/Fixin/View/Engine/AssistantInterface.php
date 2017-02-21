<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Engine;

interface AssistantInterface
{
    /**
     * New instance for engine
     */
    public function withEngine(EngineInterface $engine): AssistantInterface;
}
