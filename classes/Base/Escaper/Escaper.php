<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Escaper;

use Fixin\Resource\Resource;

class Escaper extends Resource implements EscaperInterface
{
    protected const
        HTML_ENCODING_OPTIONS = ENT_COMPAT | ENT_HTML5 | ENT_SUBSTITUTE,
        JS_ENCODING_OPTIONS = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION,
        JS_REPLACES = ['"' => "'", ' ' => '\x20'];

    /**
     * @inheritDoc
     */
    public function escapeHtml(string $string): string
    {
        return htmlspecialchars($string, static::HTML_ENCODING_OPTIONS);
    }

    /**
     * @inheritDoc
     */
    public function escapeJs(string $string): string
    {
        return mb_substr($this->escapeJsVariable($string), 1, -1);
    }

    /**
     * @inheritDoc
     */
    public function escapeJsVariable($variable): string
    {
        return strtr(json_encode($variable, static::JS_ENCODING_OPTIONS), static::JS_REPLACES);
    }

    /**
     * @inheritDoc
     */
    public function escapeUrl(string $url): string
    {
        return rawurlencode($url);
    }
}
