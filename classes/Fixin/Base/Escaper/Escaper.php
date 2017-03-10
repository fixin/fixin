<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Escaper;

use Fixin\Resource\Resource;

class Escaper extends Resource implements EscaperInterface
{
    protected const
        HTML_ENCODING_OPTIONS = ENT_COMPAT | ENT_HTML5 | ENT_SUBSTITUTE,
        JS_ENCODING_OPTIONS = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION,
        JS_REPLACES = ['"' => "'", ' ' => '\x20'];

    public function escapeHtml(string $string): string
    {
        return htmlspecialchars($string, static::HTML_ENCODING_OPTIONS);
    }

    public function escapeJs(string $string): string
    {
        return mb_substr($this->escapeJsVariable($string), 1, -1);
    }

    public function escapeJsVariable($var): string
    {
        return strtr(json_encode($var, static::JS_ENCODING_OPTIONS), static::JS_REPLACES);
    }

    public function escapeUrl(string $url): string
    {
        return rawurlencode($url);
    }
}
