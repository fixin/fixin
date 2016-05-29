<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Helper;

use Fixin\View\Engine\EngineInterface;
use Fixin\Resource\ResourceInterface;

interface HelperInterface extends ResourceInterface {

    /**
     * New instance for engine
     *
     * @param EngineInterface $engine
     * @return self
     */
    public function withEngine(EngineInterface $engine): HelperInterface;
}