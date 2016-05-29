<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Helper;

use Fixin\View\Engine\EngineInterface;
use Fixin\Resource\Resource;

abstract class Helper extends Resource implements HelperInterface {

    /**
     * @var EngineInterface
     */
    protected $engine;

    /**
     * {@inheritDoc}
     * @see \Fixin\View\Helper\HelperInterface::withEngine($engine)
     */
    public function withEngine(EngineInterface $engine): HelperInterface {
        $clone = clone $this;
        $clone->engine = $engine;

        return $clone;
    }
}