<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Helper;

use Fixin\Resource\Prototype;
use Fixin\View\Engine\EngineInterface;

abstract class Helper extends Prototype implements HelperInterface
{
    /**
     * @var EngineInterface
     */
    protected $engine;

    protected function setEngine(EngineInterface $engine): void
    {
        $this->engine = $engine;
    }
}
