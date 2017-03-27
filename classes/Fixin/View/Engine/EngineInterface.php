<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\View\Engine;

use Fixin\Resource\ResourceInterface;
use Fixin\View\Helper\HelperInterface;
use Fixin\View\ViewInterface;

interface EngineInterface extends ResourceInterface
{

    /**
     * Get type of rendered content
     */
    public function getContentType(): string;

    public function getHelper(string $name): HelperInterface;
    public function render(ViewInterface $view);
}
