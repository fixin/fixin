<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Escaper;

interface EscaperInterface {

    /**
     * Escape a string for HTML
     *
     * @param string $string
     * @return string
     */
    public function escapeHtml(string $string): string;
}