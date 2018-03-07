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
     *
     * @return string
     */
    public function getContentType(): string;

    /**
     * Get helper
     *
     * @param string $name
     * @return HelperInterface
     */
    public function getHelper(string $name): HelperInterface;

    /**
     * Render
     *
     * @param ViewInterface $view
     * @return mixed
     */
    public function render(ViewInterface $view);
}
