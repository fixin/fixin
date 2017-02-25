<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Escaper;

interface EscaperInterface
{
    /**
     * Escape string for HTML
     */
    public function escapeHtml(string $string): string;

    /**
     * Escape string for JavaScript
     */
    public function escapeJs(string $string): string;

    /**
     * Escape variable for JavaScript
     */
    public function escapeJsVariable($var): string;

    /**
     * Escape string for URI
     */
    public function escapeUrl(string $url): string;
}
