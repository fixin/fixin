<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Escaper;

use Fixin\Resource\ResourceInterface;

interface EscaperInterface extends ResourceInterface
{
    /**
     * Escape string for HTML
     *
     * @param string $string
     * @return string
     */
    public function escapeHtml(string $string): string;

    /**
     * Escape string for JavaScript
     *
     * @param string $string
     * @return string
     */
    public function escapeJs(string $string): string;

    /**
     * Escape variable for JavaScript
     *
     * @param mixed $variable
     * @return string
     */
    public function escapeJsVariable($variable): string;

    /**
     * Escape string for URI
     *
     * @param string $url
     * @return string
     */
    public function escapeUrl(string $url): string;
}
