<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\View\Helper;

use Fixin\Resource\Prototype;
use Fixin\View\Engine\EngineInterface;

abstract class AbstractHelper extends Prototype implements HelperInterface
{
    protected const
        THIS_SETS = [
            self::ENGINE => EngineInterface::class
        ];

    /**
     * @var EngineInterface
     */
    protected $engine;
}
