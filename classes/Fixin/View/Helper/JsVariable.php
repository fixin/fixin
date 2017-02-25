<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Helper;

use Fixin\Base\Escaper\EscaperInterface;
use Fixin\Resource\ResourceManagerInterface;

class JsVariable extends EscapeHelper
{
    public function escape($value): string
    {
        return $this->escaper->escapeJsVariable($value);
    }
}
