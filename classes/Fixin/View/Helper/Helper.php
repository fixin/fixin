<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Helper;

use Fixin\View\Engine\EngineInterface;
use Fixin\Resource\Prototype;

abstract class Helper extends Prototype implements HelperInterface {

    /**
     * @var EngineInterface
     */
    protected $engine;

    /**
     * Set engine instance
     *
     * @param EngineInterface $engine
     */
    protected function setEngine(EngineInterface $engine) {
        $this->engine = $engine;
    }
}