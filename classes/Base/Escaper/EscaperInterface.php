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
     */
    public function escapeHtml(string $string): string;

    /**
     * Escape string for JavaScript
     */
    public function escapeJs(string $string): string;

    /**
     * Escape variable for JavaScript
     */
    public function escapeJsVariable($variable): string;

    /**
     * Escape string for URI
     */
    public function escapeUrl(string $url): string;
}
