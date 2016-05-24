<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Escaper;

interface EscaperInterface {

    /**
     * Encode variable for JavaScript
     *
     * @param mixed $string
     * @return string
     */
    public function encodeJsVariable($string): string;

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
     * Escape string for URI
     *
     * @param string $url
     * @return string
     */
    public function escapeUrl(string $url): string;
}